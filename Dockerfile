FROM prestashop/prestashop:latest

# PHP tuning commun
RUN { \
  echo "memory_limit=2048M"; \
  echo "upload_max_filesize=128M"; \
  echo "post_max_size=128M"; \
  echo "max_execution_time=300"; \
  echo "max_input_vars=10000"; \
  echo "realpath_cache_size=4096K"; \
  echo "realpath_cache_ttl=600"; \
} > /usr/local/etc/php/conf.d/99-custom.ini

# OPCache dual mode
ARG APP_ENV=prod
RUN if [ "$APP_ENV" = "dev" ]; then \
    echo "opcache.enable=1" > /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.memory_consumption=512" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.interned_strings_buffer=64" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.max_accelerated_files=60000" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.save_comments=1" >> /usr/local/etc/php/conf.d/10-opcache.ini; \
  else \
    echo "opcache.enable=1" > /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.memory_consumption=512" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.interned_strings_buffer=64" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.max_accelerated_files=60000" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/10-opcache.ini && \
    echo "opcache.save_comments=1" >> /usr/local/etc/php/conf.d/10-opcache.ini; \
  fi

# Fix des droits
RUN chown -R www-data:www-data /var/www/html