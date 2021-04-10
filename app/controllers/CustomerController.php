<?php
require_once(APPROOT . "/classes/Customer.php");
require_once(APPROOT . "/helper/PasswordHelper.php");
require_once(APPROOT . "/helper/MailHelper.php");

class CustomerController extends Autoloader {
    private $data = [
        "status" => "unset",
    ];

    public function guestLogin() {
        if (isset($_POST["emailAddress"])) {
            $this->data["status"] = $this->guestLoginAction(
                $_POST["emailAddress"]
            );

            if ($this->data["status"] === "success") {
                if (isset($_GET["callback"])) {
                    header("Location: " . URLROOT . "/" . $_GET["callback"]);
                } else {
                    header("Location: " . URLROOT);
                }

                return;
            }
        }

        $this->view("customer/login", $this->data);
    }

    private function guestLoginAction($emailAddress) {
        try {
            // Perform input validation
            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) return "noEmail";

            // Check if a customer already exists with that email address
            $customerModel = $this->model("Customer");
            $existingCustomer = $customerModel->getByEmailAddress($emailAddress);

            if ($existingCustomer === null) {
                // Create the customer in the database
                $customerModel->createCustomer(
                    null,
                    $emailAddress,
                    null
                );

                $existingCustomer = $customerModel->getByEmailAddress($emailAddress);
            } else if (!$existingCustomer->getIsGuest()) {
                // If the existing customer is not a guest account, it should login through the normal login form
                return "fullAccountFound";
            }

            // Set the session
            $_SESSION["loggedInCustomer"] = $existingCustomer;

            return "success";
        } catch (Exception $e) {
            return "internalServerError";
        }
    }

    public function login(){
        // Check if this is a POST
        if (isset($_POST["emailAddress"], $_POST["password"])) {
            // Set the status
            $this->data["status"] = $this->loginAction(
                $_POST["emailAddress"],
                $_POST["password"]
            );

            // Check if the result was successful and route accordingly
            if ($this->data["status"] === "success") {
                if (isset($_GET["callback"])) {
                    header("Location: " . URLROOT . "/" . $_GET["callback"]);
                } else {
                    header("Location: " . URLROOT);
                }

                return;
            }
        }

        $this->view("customer/login", $this->data);
    }

    private function loginAction($emailAddress, $password) {
        try {
            // Perform input validation
            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) return "noEmail";

            // Check if the customer exists
            $customerModel = $this->model("Customer");
            $existingCustomer = $customerModel->getByEmailAddress($emailAddress);
            if ($existingCustomer === null) return "noUser";
            if ($existingCustomer->getIsGuest()) return "guestAccount";

            // Check if the password matches
            $correctPassword = PasswordHelper::check($password, $existingCustomer->getPassword());
            if (!$correctPassword) return "invalidPassword";

            // Set the session
            $_SESSION["loggedInCustomer"] = $existingCustomer;

            return "success";
        } catch (Exception $e) {
            return "internalServerError";
        }
    }

    public function register(){
        // Check if this is a post from the UI
        if (isset($_POST["name"], $_POST["emailAddress"], $_POST["password"], $_POST["repeatPassword"])) {
            $this->data["status"] = $this->registerAction(
                $_POST["name"],
                $_POST["emailAddress"],
                $_POST["password"],
                $_POST["repeatPassword"]
            );

            if ($this->data["status"] === "success") {
                if (isset($_GET["callback"])) {
                    header("Location: " . URLROOT . "/" . $_GET["callback"]);
                } else {
                    header("Location: " . URLROOT);
                }

                return;
            }
        }

        $this->view('customer/register', $this->data);
    }

    private function registerAction($name, $emailAddress, $password, $repeatPassword) {
        try {
            // Perform input validation
            if (strlen($name) === 0) return "noName";
            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) return "noEmail";
            if (!PasswordHelper::isStrongPassword($password)) return "notStrongPassword";
            if ($password !== $repeatPassword) return "passwordsDontMatch";

            // Check if a customer already exists with the given email address
            $isGuestAccount = false;
            $customerModel = $this->model("Customer");
            $existingCustomer = $customerModel->getByEmailAddress($emailAddress);
            if ($existingCustomer !== null) {
                $isGuestAccount = $existingCustomer->getIsGuest();

                if (!$isGuestAccount) return "emailAddressInUse";
            };

            // Create an password hash and add the user to the database
            $passwordHash = PasswordHelper::hash($password);

            if (!$isGuestAccount) {
                $customerModel->createCustomer(
                    $name,
                    $emailAddress,
                    $passwordHash
                );
            } else {
                if (isset($_GET["token"])) {
                    $token = $_GET["token"];
                    $isValidToken = PasswordHelper::check($emailAddress . EMAILCHECKSALT . "confirm email", $token);

                    if ($isValidToken) {
                        $customerModel->updateAccountDetails(
                            $existingCustomer->getId(),
                            $name,
                            $emailAddress,
                            $passwordHash
                        );
                    } else {
                        return "invalidToken";
                    }
                } else {
                    $emailConfirmationToken = PasswordHelper::hash($emailAddress . EMAILCHECKSALT . "confirm email");
                    $confirmationUrl = URLROOT . "/customer/register?token=" . urlencode($emailConfirmationToken);

                    MailHelper::send(
                        $emailAddress,
                        "Confirm email address",
                        '<h1>Password reset</h1>
                        <p>
                            You need to confirm your email address because this email address was used before using
                            the guest login.
                        </p>
                        <a href="' . $confirmationUrl . '">' . $confirmationUrl . '</a>'
                    );

                    return "confirmationRequired";
                }
            }

            // Retrieve the new customer from the database which now includes an id and set the session
            $customer = $customerModel->getByEmailAddress($emailAddress);
            $_SESSION["loggedInCustomer"] = $customer;

            return "success";
        } catch (Exception $e) {
            return "internalServerError";
        }
    }

    public function logout() {
        $_SESSION["loggedInCustomer"] = null;
        header("Location: " . URLROOT);
    }

    public function resetPassword() {
        if (isset($_POST["emailAddress"])){
            $this->data["status"] = $this->resetPasswordAction(
                $_POST["emailAddress"]
            );
        }

        $this->view("customer/resetPassword", $this->data);
    }

    private function resetPasswordAction($emailAddress) {
        try {
            // Perform input validation
            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) return "noEmail";

            // Check if the customer exists and if it's a full account
            $customerModel = $this->model("Customer");
            $existingCustomer = $customerModel->getByEmailAddress($emailAddress);
            if ($existingCustomer === null) return "noCustomer";
            if ($existingCustomer->getIsGuest()) return "isGuestAccount";

            // Create a hash to be used as a token to check if the email belongs to the user
            $currentDate = new Datetime();
            $dateString = $currentDate->format("Y-m-d");
            $emailConfirmationToken = PasswordHelper::hash($emailAddress . $dateString . EMAILCHECKSALT);

            $confirmationUrl = URLROOT . "/customer/confirmPasswordReset?token=" . urlencode($emailConfirmationToken);

            MailHelper::send(
                $emailAddress,
                "Password reset",
                '
                <h1>Password reset</h1>
                <p>
                    This email was send to your email address because there was a request for a password reset.
                    If you did not request to reset your password you can ignore this email. This link is only
                    valid on this day.
                </p>
                <a href="' . $confirmationUrl . '">' . $confirmationUrl . '</a>
            '
            );

            return "success";
        } catch (Exception $e) {
            return "internalServerError";
        }
    }

    public function confirmPasswordReset() {
        if (!isset($_GET["token"])) {
            $this->view("customer/login", $this->data);
        } else {
            if (isset($_POST["emailAddress"], $_POST["password"], $_POST["repeatPassword"])) {
                $this->data["status"] = $this->confirmPasswordResetAction(
                    $_GET["token"],
                    $_POST["emailAddress"],
                    $_POST["password"],
                    $_POST["repeatPassword"]
                );
            }

            $this->view("customer/confirmPasswordReset", $this->data);
        }
    }

    private function confirmPasswordResetAction($token, $emailAddress, $password, $repeatPassword) {
        try {
            // Perform input validation
            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) return "noEmail";
            if (!PasswordHelper::isStrongPassword($password)) return "notStrongPassword";
            if ($password !== $repeatPassword) return "passwordsDontMatch";

            // Check if a customer already exists with the given email address
            $customerModel = $this->model("Customer");
            $existingCustomer = $customerModel->getByEmailAddress($emailAddress);
            if ($existingCustomer === null) return "noCustomer";

            // Check the token
            $currentDate = new Datetime();
            $dateString = $currentDate->format("Y-m-d");
            $isCorrectToken = PasswordHelper::check($emailAddress . $dateString . EMAILCHECKSALT, $token);
            if (!$isCorrectToken) return "invalidURL";

            // Set the new password for the customer
            $passwordHash = PasswordHelper::hash($password);
            $customerModel->updateCustomerPassword($existingCustomer->getId(), $passwordHash);

            return "success";
        } catch (Exception $e) {
            return "internalServerError";
        }
    }
}