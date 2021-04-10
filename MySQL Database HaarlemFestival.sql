CREATE DATABASE `HaarlemFestival`;
USE `HaarlemFestival`;

# DROP DATABASE `HaarlemFestival`;

CREATE TABLE `Customer`
(
    `CustomerId`   int          not null auto_increment primary key,
    `Name`         varchar(255) null,
    `EmailAddress` varchar(255) not null,
    `Password`     varchar(255) null
);

CREATE UNIQUE INDEX UniqueCustomerEmailAddress ON `Customer` (`EmailAddress`);

CREATE TABLE `Invoice`
(
    `InvoiceId`     int          not null auto_increment primary key,
    `PurchaseDate`  datetime     not null,
    `PaymentStatus` varchar(255) not null,
    `CustomerId`    int          null,
    constraint foreign key (`CustomerId`) references `Customer` (`CustomerId`)
        on update cascade
        on delete set null
);

CREATE TABLE `Event`
(
    `EventId`     int           not null auto_increment primary key,
    `Name`        varchar(255)  not null,
    `ShortName`   varchar(255)  not null,
    `Description` varchar(2048) not null,
    `ImageId`     varchar(255)  not null
);

CREATE TABLE `Ticket`
(
    `TicketId` int          not null auto_increment primary key,
    `Name`     varchar(255) not null,
    `Price`    float        not null,
    `EventId`  int          not null,
    constraint foreign key (`EventId`) references `Event` (`EventId`)
        on update cascade
        on delete cascade
);

CREATE TABLE `SelectedTicket`
(
    `SelectedTicketId` int          not null auto_increment primary key,
    `Price`            float        not null,
    `Participants`     int          not null,
    `Comment`          varchar(512) null,
    `InvoiceId`        int          not null,
    constraint foreign key (`InvoiceId`) references `Invoice` (`InvoiceId`)
        on update cascade
        on delete cascade,
    `TicketId`         int          null,
    constraint foreign key (`TicketId`) references `Ticket` (`TicketId`)
        on update cascade
        on delete set null
);

CREATE TABLE `Location`
(
    `LocationId`  int           not null auto_increment primary key,
    `Name`        varchar(255)  not null,
    `Description` varchar(2048) null,
    `ImageId`     varchar(255)  not null,
    `Address`     varchar(255)  not null,
    `Seats`       int           null,
    `Genre`       varchar(255)  null,
    `Stars`       DECIMAL(2, 1) null,
    `KidsMenu`    BIT(1)        null,
    `EventId`     int           not null,
    constraint foreign key (`EventId`) references `Event` (`EventId`)
        on update cascade
        on delete cascade
);

CREATE TABLE `Hall`
(
    `HallId`     int          not null auto_increment primary key,
    `Name`       varchar(255) not null,
    `Seats`      int          not null,
    `LocationId` int          not null,
    constraint foreign key (`LocationId`) references `Location` (`LocationId`)
        on update cascade
        on delete cascade
);

CREATE TABLE `Employee`
(
    `EmployeeId`   int          not null auto_increment primary key,
    `Name`         varchar(255) not null,
    `EmailAddress` varchar(255) not null,
    `Password`     varchar(255) not null,
    `UserRole`     varchar(255) not null -- Either 'System', 'Admin', 'Editor', 'Volunteer'
);

CREATE UNIQUE INDEX UniqueEmployeeEmailAddress ON `Employee` (`EmailAddress`);

CREATE TABLE `Activity`
(
    `ActivityId`    int          not null auto_increment primary key,
    `Name`          varchar(255) not null,
    `Capacity`      int          null,
    `Language`      char(15)     null, -- Example: EN, NL, CH
    `StartDateTime` datetime     not null,
    `EndDateTime`   datetime     not null,
    `EventId`       int          not null,
    constraint foreign key (`EventId`) references `Event` (`EventId`)
        on update cascade
        on delete cascade,
    `LocationId`    int          null,
    constraint foreign key (`LocationId`) references `Location` (`LocationId`)
        on update cascade
        on delete no action,
    `HallId`        int          null,
    constraint foreign key (`HallId`) references `Hall` (`HallId`)
        on update cascade
        on delete no action,
    `EmployeeId`    int          null,
    constraint foreign key (`EmployeeId`) references `Employee` (`EmployeeId`)
        on update cascade
        on delete no action
);


CREATE TABLE `Artist`
(
    `ArtistId`    int           not null auto_increment primary key,
    `Name`        varchar(255)  not null,
    `ImageId`     varchar(255)  not null,
    `Description` varchar(2048) not null,
    `EventId`     int           not null,
    constraint foreign key (`EventId`) references `Event` (`EventId`)
        on update cascade
        on delete cascade
);

CREATE TABLE `ActivityArtistLink`
(
    `ActivityId` int not null,
    constraint foreign key (`ActivityId`) references `Activity` (`ActivityId`)
        on update cascade
        on delete cascade,
    `ArtistId`   int not null,
    constraint foreign key (`ArtistId`) references `Artist` (`ArtistId`)
        on update cascade
        on delete cascade,
    primary key (`ActivityId`, `ArtistId`)
);

CREATE TABLE `TicketActivityLink`
(
    `TicketId`   int not null,
    constraint foreign key (`TicketId`) references `Ticket` (`TicketId`)
        on update cascade
        on delete cascade,
    `ActivityId` int not null,
    constraint foreign key (`ActivityId`) references `Activity` (`ActivityId`)
        on update cascade
        on delete cascade,
    primary key (`TicketId`, `ActivityId`)
);

CREATE TABLE `InfoPages`
(
    `PageId`    int           not null auto_increment primary key,
    `PageTitle` varchar(255)  not null,
    `Content`   varchar(8192) not null
);