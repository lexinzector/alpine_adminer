ARG ARCH=
FROM docker.io/lexinzector/alpine_php_fpm:7.4-12${ARCH}

ADD files /src/files
RUN cd ~; \
	cp -rf /src/files/etc/* /etc/; \
	cp -rf /src/files/var/* /var/; \
	rm -rf /src/files; \
	chmod +x /root/run.sh; \
	echo 'Ok'