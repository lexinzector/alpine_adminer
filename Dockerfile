# Получаем архитектуру от Docker (по умолчанию)
ARG TARGETPLATFORM

# Используем базовый образ без явного указания платформы
FROM docker.io/lexinzector/alpine_php_fpm:7.4-12

# Копируем файлы
ADD files /src/files

# Настраиваем окружение
RUN cd ~ && \
    cp -rf /src/files/etc/* /etc/ && \
    cp -rf /src/files/var/* /var/ && \
    rm -rf /src/files && \
    chmod +x /root/run.sh && \
    echo 'Ok'
