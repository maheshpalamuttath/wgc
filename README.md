sudo apt update
sudo apt install apache2
sudo apt install php libapache2-mod-php php-curl php-json
cd /var/www/html/
sudo git clone https://github.com/maheshpalamuttath/wpc.git
sudo chown -R www-data:www-data /var/www/html/wgc/
sudo chmod -R 755 /var/www/html/wgc/
sudo vim /etc/apache2/sites-available/wgc.conf

<VirtualHost *:8002>
    ServerAdmin webmaster@example.com
    DocumentRoot /var/www/html/wgc/

    <Directory /var/www/html/wgc/>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

sudo a2ensite wgc.conf
sudo systemctl reload apache2


sudo vim /etc/apache2/ports.conf

add "Listen 8003"

sudo ufw allow 8002/tcp
sudo systemctl reload apache2 && sudo systemctl restart apache2
