CREATE DATABASE
IF NOT EXISTS cinemax
CHARACTER
SET utf8mb4
COLLATE utf8mb4_unicode_ci;
USE cinemax;

--Mô tả:


-- Bảng Users --
CREATE TABLE users
(
  UserID INT
  AUTO_INCREMENT PRIMARY KEY,
    FullName VARCHAR
  (100) NOT NULL,
    Email VARCHAR
  (100) NOT NULL UNIQUE,
    PasswordHash VARCHAR
  (255) NOT NULL,
    Phone VARCHAR
  (20),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

  -- Bảng Locations và Cinema Status --
  CREATE TABLE locations
  (
    LocationID INT
    AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR
    (100) NOT NULL UNIQUE
);

    CREATE TABLE cinema_status
    (
      StatusID INT
      AUTO_INCREMENT PRIMARY KEY,
    StatusName VARCHAR
      (50) NOT NULL UNIQUE
);

      CREATE TABLE cinemas
      (
        CinemaID INT
        AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR
        (150) NOT NULL,
    Address VARCHAR
        (255) NOT NULL,
    LocationID INT NOT NULL,
    StatusID INT NOT NULL,
    FOREIGN KEY
        (LocationID) REFERENCES locations
        (LocationID),
    FOREIGN KEY
        (StatusID) REFERENCES cinema_status
        (StatusID)
);

        -- Bảng Movies, Genres, Formats --
        CREATE TABLE movie_status
        (
          StatusID INT
          AUTO_INCREMENT PRIMARY KEY,
    StatusName VARCHAR
          (50) NOT NULL UNIQUE
);

          CREATE TABLE movies
          (
            MovieID INT
            AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR
            (255) NOT NULL,
    Description TEXT,
    Duration INT NOT NULL,
    ReleaseDate DATE,
    StatusID INT NOT NULL,
    FOREIGN KEY
            (StatusID) REFERENCES movie_status
            (StatusID)
);

            CREATE TABLE genres
            (
              GenreID INT
              AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR
              (100) NOT NULL UNIQUE
);

              CREATE TABLE movie_genre
              (
                MovieID INT NOT NULL,
                GenreID INT NOT NULL,
                PRIMARY KEY (MovieID, GenreID),
                FOREIGN KEY (MovieID) REFERENCES movies(MovieID) ON DELETE CASCADE,
                FOREIGN KEY (GenreID) REFERENCES genres(GenreID) ON DELETE CASCADE
              );

              CREATE TABLE formats
              (
                FormatID INT
                AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR
                (50) NOT NULL UNIQUE
);

                CREATE TABLE movie_format
                (
                  MovieID INT NOT NULL,
                  FormatID INT NOT NULL,
                  PRIMARY KEY (MovieID, FormatID),
                  FOREIGN KEY (MovieID) REFERENCES movies(MovieID) ON DELETE CASCADE,
                  FOREIGN KEY (FormatID) REFERENCES formats(FormatID)
                );

                --Bảng Halls và Seats --
                CREATE TABLE hall_status
                (
                  StatusID INT
                  AUTO_INCREMENT PRIMARY KEY,
    StatusName VARCHAR
                  (50) NOT NULL UNIQUE
);

                  CREATE TABLE halls
                  (
                    HallID INT
                    AUTO_INCREMENT PRIMARY KEY,
    CinemaID INT NOT NULL,
    Name VARCHAR
                    (50) NOT NULL,
    StatusID INT NOT NULL,
    FOREIGN KEY
                    (CinemaID) REFERENCES cinemas
                    (CinemaID),
    FOREIGN KEY
                    (StatusID) REFERENCES hall_status
                    (StatusID)
);

                    CREATE TABLE seat_type
                    (
                      SeatTypeID INT
                      AUTO_INCREMENT PRIMARY KEY,
    TypeName VARCHAR
                      (50) NOT NULL,
    PriceMultiplier DECIMAL
                      (5,2) DEFAULT 1.0
);

                      CREATE TABLE seats
                      (
                        SeatID INT
                        AUTO_INCREMENT PRIMARY KEY,
    HallID INT NOT NULL,
    SeatTypeID INT NOT NULL,
    RowName CHAR
                        (2) NOT NULL,
    SeatNumber INT NOT NULL,
    FOREIGN KEY
                        (HallID) REFERENCES halls
                        (HallID),
    FOREIGN KEY
                        (SeatTypeID) REFERENCES seat_type
                        (SeatTypeID),
    UNIQUE
                        (HallID, RowName, SeatNumber)
);

                        --Bảng Images --
                        CREATE TABLE images
                        (
                          ImageID INT
                          AUTO_INCREMENT PRIMARY KEY,
    MovieID INT NOT NULL,
    ImageUrl VARCHAR
                          (255) NOT NULL,
    FOREIGN KEY
                          (MovieID) REFERENCES movies
                          (MovieID) ON
                          DELETE CASCADE
);

                          --Bảng Shows (Suất chiếu) --
                          CREATE TABLE shows
                          (
                            ShowID INT
                            AUTO_INCREMENT PRIMARY KEY,
    MovieID INT NOT NULL,
    HallID INT NOT NULL,
    FormatID INT NOT NULL,
    ShowDate DATE NOT NULL,
    StartTime TIME NOT NULL,
    EndTime TIME NOT NULL,
    FOREIGN KEY
                            (MovieID) REFERENCES movies
                            (MovieID),
    FOREIGN KEY
                            (HallID) REFERENCES halls
                            (HallID),
    FOREIGN KEY
                            (FormatID) REFERENCES formats
                            (FormatID)
);

                            -- Bảng Bill, Combo, Promotions, Tickets--

                            CREATE TABLE bill_status
                            (
                              StatusID INT
                              AUTO_INCREMENT PRIMARY KEY,
    StatusName VARCHAR
                              (50) NOT NULL UNIQUE
);

                              CREATE TABLE bills
                              (
                                BillID INT
                                AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    TotalAmount DECIMAL
                                (10,2) NOT NULL,
    StatusID INT NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY
                                (UserID) REFERENCES users
                                (UserID),
    FOREIGN KEY
                                (StatusID) REFERENCES bill_status
                                (StatusID)
);

                                CREATE TABLE tickets
                                (
                                  TicketID INT
                                  AUTO_INCREMENT PRIMARY KEY,
    BillID INT NOT NULL,
    ShowID INT NOT NULL,
    SeatID INT NOT NULL,
    Price DECIMAL
                                  (10,2) NOT NULL,
    FOREIGN KEY
                                  (BillID) REFERENCES bills
                                  (BillID) ON
                                  DELETE CASCADE,
    FOREIGN KEY (ShowID)
                                  REFERENCES shows
                                  (ShowID),
    FOREIGN KEY
                                  (SeatID) REFERENCES seats
                                  (SeatID),
    UNIQUE
                                  (ShowID, SeatID)
);

                                  CREATE TABLE combos
                                  (
                                    ComboID INT
                                    AUTO_INCREMENT PRIMARY KEY,
    ComboName VARCHAR
                                    (100) NOT NULL,
    Description VARCHAR
                                    (255),
    Price DECIMAL
                                    (10,2) NOT NULL,
    IsActive BOOLEAN DEFAULT TRUE
);

                                    CREATE TABLE bill_combo
                                    (
                                      BillID INT NOT NULL,
                                      ComboID INT NOT NULL,
                                      Quantity INT NOT NULL DEFAULT 1,
                                      Price DECIMAL(10,2) NOT NULL,
                                      PRIMARY KEY (BillID, ComboID),
                                      FOREIGN KEY (BillID) REFERENCES bills(BillID) ON DELETE CASCADE,
                                      FOREIGN KEY (ComboID) REFERENCES combos(ComboID)
                                    );

                                    CREATE TABLE promotions
                                    (
                                      PromotionID INT
                                      AUTO_INCREMENT PRIMARY KEY,
    PromotionCode VARCHAR
                                      (50) NOT NULL UNIQUE,
    PromotionName VARCHAR
                                      (150) NOT NULL,

    DiscountPercent DECIMAL
                                      (5,2) NOT NULL
        CHECK
                                      (DiscountPercent > 0 AND DiscountPercent <= 100),

    StartDate DATETIME NOT NULL,
    EndDate DATETIME NOT NULL,

    MinBillAmount DECIMAL
                                      (10,2) DEFAULT 0,

    IsActive BOOLEAN DEFAULT TRUE,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

                                      ALTER TABLE bills
  ADD COLUMN PromotionID INT NULL,
                                      ADD COLUMN DiscountAmount DECIMAL
                                      (10,2) NOT NULL DEFAULT 0,
                                      ADD CONSTRAINT fk_bills_promotions FOREIGN KEY
                                      (PromotionID) REFERENCES promotions
                                      (PromotionID);






                                      --Bảng roles, permissions, role_permissions và user_roles --
                                      CREATE TABLE roles
                                      (
                                        RoleID INT
                                        AUTO_INCREMENT PRIMARY KEY,
    RoleName VARCHAR
                                        (50) NOT NULL UNIQUE,
    Description VARCHAR
                                        (255)
);


                                        CREATE TABLE permissions
                                        (
                                          PermissionID INT
                                          AUTO_INCREMENT PRIMARY KEY,
    PermissionCode VARCHAR
                                          (100) NOT NULL UNIQUE,
    Description VARCHAR
                                          (255)
);


                                          CREATE TABLE role_permission
                                          (
                                            RoleID INT NOT NULL,
                                            PermissionID INT NOT NULL,
                                            PRIMARY KEY (RoleID, PermissionID),
                                            FOREIGN KEY (RoleID) REFERENCES roles(RoleID) ON DELETE CASCADE,
                                            FOREIGN KEY (PermissionID) REFERENCES permissions(PermissionID) ON DELETE CASCADE
                                          );

                                          CREATE TABLE user_role
                                          (
                                            UserID INT NOT NULL,
                                            RoleID INT NOT NULL,
                                            PRIMARY KEY (UserID, RoleID),
                                            FOREIGN KEY (UserID) REFERENCES users(UserID) ON DELETE CASCADE,
                                            FOREIGN KEY (RoleID) REFERENCES roles(RoleID) ON DELETE CASCADE
                                          );


