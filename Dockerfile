FROM php:7.4-apache

# อัปเดตและติดตั้ง extension ที่จำเป็นสำหรับ CodeIgniter 3
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql zip

# เปิดใช้งาน Apache mod_rewrite
RUN a2enmod rewrite

# ตั้งค่า Working Directory
WORKDIR /var/www/html

# อนุญาตให้ใช้ .htaccess overrides ได้
RUN echo "<Directory /var/www/html>\n\tAllowOverride All\n</Directory>" > /etc/apache2/conf-available/allowoverride.conf \
    && a2enconf allowoverride

# ปรับสิทธิ์โฟลเดอร์เริ่มต้นให้ www-data
RUN chown -R www-data:www-data /var/www/html
