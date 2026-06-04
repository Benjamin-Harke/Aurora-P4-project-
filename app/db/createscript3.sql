-- Accounts table for authentication
CREATE TABLE IF NOT EXISTS Accounts
(
     Id                  INT             NOT NULL AUTO_INCREMENT
    ,Email               VARCHAR(255)    NOT NULL UNIQUE
    ,Password            VARCHAR(255)    NOT NULL
    ,FirstName           VARCHAR(100)    NOT NULL
    ,LastName            VARCHAR(100)    NOT NULL
    ,PhoneNumber         VARCHAR(20)     NULL
    ,CreatedAt           DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
    ,UpdatedAt           DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ,IsActive            BIT             NOT NULL DEFAULT 1

    ,PRIMARY KEY (Id)
);
