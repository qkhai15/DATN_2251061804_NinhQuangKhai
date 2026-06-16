# Hệ thống quản lý nhà trọ tích hợp OCR và Chatbot

## Thông tin đề tài

- Sinh viên: Ninh Quang Khải
- Mã sinh viên: 2251061804
- Lớp: 64CNTT2
- Giảng viên hướng dẫn: TS. Nguyễn Bá Quảng
- Trường Đại học Thủy Lợi

## Giới thiệu

Hệ thống hỗ trợ quản lý phòng trọ, người thuê, hợp đồng, chỉ số điện nước, hóa đơn, thông báo, yêu cầu sửa chữa, OCR và chatbot AI.

## Công nghệ sử dụng

- Laravel 9, PHP
- Blade, Tailwind CSS
- MySQL
- Python Flask
- OpenCV, PaddleOCR
- OpenRouter API

## Chức năng chính

### Admin

- Quản lý khu trọ và phòng
- Quản lý người thuê và hợp đồng
- Quản lý dịch vụ, thẻ xe
- Chốt chỉ số điện nước bằng OCR
- Lập và quản lý hóa đơn
- Gửi thông báo
- Xử lý yêu cầu sửa chữa
- Xem thống kê

### Người thuê

- Xem thông tin phòng
- Xem hóa đơn
- Xem thông báo
- Cập nhật hồ sơ
- Gửi yêu cầu sửa chữa
- Sử dụng chatbot AI

## Cài đặt Laravel

```bash
git clone https://github.com/qkhai15/DATN_2251061804_NinhQuangKhai.git
cd DATN_2251061804_NinhQuangKhai
composer install
cp .env.example .env
php artisan key:generate
