CREATE DATABASE IF NOT EXISTS BookShop;
USE BookShop;

CREATE TABLE Books (
  BookID INT AUTO_INCREMENT PRIMARY KEY,
  Title VARCHAR(200),
  Author VARCHAR(100),
  Price DECIMAL(18,0),
  Stock INT DEFAULT 0,
  ImageURL VARCHAR(255) DEFAULT 'assets/images/default-book.jpg',
  Description VARCHAR(500),
  TheLoai VARCHAR(50)
);

CREATE TABLE users (
  UserID INT AUTO_INCREMENT PRIMARY KEY,
  FullName VARCHAR(100),
  Email VARCHAR(100) UNIQUE,
  BirthDate DATE,
  Password VARCHAR(255),
  Address VARCHAR(255),
  Phone VARCHAR(15),
  Role VARCHAR(20) DEFAULT 'Customer'
);

CREATE TABLE Orders (
  OrderID INT AUTO_INCREMENT PRIMARY KEY,
  UserID INT NOT NULL,
  TongTien DECIMAL(10,0),
  NgayDat DATETIME DEFAULT CURRENT_TIMESTAMP,
  TrangThai VARCHAR(50),
  FOREIGN KEY (UserID) REFERENCES users(UserID)
  ON DELETE CASCADE
  ON UPDATE CASCADE
);

INSERT INTO Books (BookID,Title,Author,Price,Stock,ImageURL,Description,TheLoai) VALUES
(14,'Đắc Nhân Tâm','Dale Carnegie',89000,50,'dac-nhan-tam.jpg','Cuốn sách kinh điển về nghệ thuật giao tiếp','tieu-thuyet'),
(15,'Nhà Giả Kim','Paulo Coelho',79000,30,'nha-gia-kim.jpg','Hành trình theo đuổi giấc mơ của chàng trai trẻ','tieu-thuyet'),
(16,'Hai Số Phận','Jeffrey Archer',55000,40,'hai-so-phan.jpg','Tiểu thuyết kể về hai người đàn ông sinh cùng ngày','truyen-ngan'),
(17,'Làn Gió Lạnh','Thạch Lam',45000,25,'lan-gio-lanh.jpg','Tập truyện ngắn lãng mạn của văn học Việt Nam','truyen-ngan'),
(18,'Hồ Sơ Máu','James Patterson',95000,20,'ho-so-mau.jpg','Tiểu thuyết trinh thám kinh dị','kinh-di'),
(19,'Hannibal','Thomas Harris',110000,15,'hannibal.jpg','Câu chuyện về tên sát nhân thông minh','kinh-di'),
(20,'Tâm Lý Học Tội Phạm','Scott Bonn',99000,35,'tam-ly-toi-pham.jpg','Phân tích tâm lý những kẻ giết người','tam-ly-toi-pham'),
(21,'Cây Cam Ngọt Của Tôi','José Mauro de Vasconcelos',85000,20,'cay-cam-ngot.jpg','Câu chuyện cảm động về tuổi thơ Zezé','tam-ly-toi-pham'),
(22,'Tuổi Trẻ Đáng Giá Bao Nhiêu','Rosie Nguyễn',75000,60,'tuoi-tre-dang-gia-bao-nhieu.jpg','Cuốn sách truyền cảm hứng','ky-nang-song'),
(23,'Nói Chuyện Là Bản Năng, Giữ Miệng Là Tu Dưỡng, Im Lặng Là Trí Tuệ','Trương Tiểu Hằng',120000,45,'im-lang-la-tri-tue.jpg','Cuốn sách dạy nghệ thuật giao tiếp','ky-nang-song');

INSERT INTO users (UserID,FullName,Email,BirthDate,Password,Address,Phone,Role) VALUES
(10,'Minh Trọng','vot750422@gmail.com','2026-03-14','$2y$10$E2DZ7JdYpr9rmgziPEg0iOmacqAZzX9o07Wsh9.ulxquUQeZIUFFO','KV Bình An ,P Bình Thạnh,Long Mỹ ,Hậu Giang','0999999999','Customer'),
(11,'Hồ hoàng như ý','hohoangnhuy@gmail.com','2026-03-15','$2y$10$mGym9cyooishg5GnwR3okuVfsTTkgSGnrB07EcY1MJcTMbfHb7zcG','KV Bình An ,P Bình Thạnh,Long Mỹ ,Hậu Giang','0987654321','Customer');

INSERT INTO Orders (OrderID,UserID,TongTien,NgayDat,TrangThai) VALUES
(3,10,200000,'2026-03-14 16:34:28','Đã thanh toán'),
(4,10,200000,'2026-03-14 16:35:36','Đã thanh toán');