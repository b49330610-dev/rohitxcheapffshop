FROM php:8.2-cli

# System dependencies install karo
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite

# Working directory
WORKDIR /app

# Sab files copy karo
COPY . .

# Permissions do
RUN chmod -R 777 /app

# Port expose karo
EXPOSE 8080

# Server start karo
CMD ["php", "-S", "0.0.0.0:8080"]