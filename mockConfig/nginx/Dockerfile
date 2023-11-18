FROM nginx:alpine

COPY nginx.conf /etc/nginx/nginx.conf

RUN apk update && apk add bash && apk add openssl

RUN openssl req -x509 -nodes -days 365 \
    -subj  "/C=CA/ST=QC/O=Company Inc/CN=*.book.local" \
    -newkey rsa:2048 -keyout /etc/ssl/private/nginx-selfsigned.key \
    -out /etc/ssl/certs/nginx-selfsigned.crt;


CMD ["nginx", "-g", "daemon off;"]