FROM yiisoftware/yii2-php:7.3-apache

# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/web|g' /etc/apache2/sites-available/000-default.conf

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer \
        --install-dir=/usr/local/bin && \
        echo "alias composer='composer'" >> /root/.bashrc && \
        composer