#!/bin/bash

SCRIPT=$(readlink -f $0)
SCRIPT_PATH=`dirname $SCRIPT`
BASE_PATH=`dirname $SCRIPT_PATH`

RETVAL=0
VERSION=4.8.1
SUBVERSION=1
IMAGE_NAME="alpine_adminer"
TAG=`date '+%Y%m%d_%H%M%S'`

case "$1" in
	
	test)
		docker build ./ -t bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-$TAG --file Dockerfile
	;;
	
	amd64)
		docker build ./ -t bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-amd64 \
			--file Dockerfile --build-arg ARCH=-amd64
	;;
	
	arm64v8)
		docker build ./ -t bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-arm64v8 \
			--file Dockerfile --build-arg ARCH=-arm64v8
	;;
	
	arm32v7)
		docker build ./ -t bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-arm32v7 \
			--file Dockerfile --build-arg ARCH=-arm32v7
	;;
	
	manifest)
		rm -rf ~/.docker/manifests/docker.io_bayrell_alpine_php_fpm-*
		
		docker push bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-amd64
		docker push bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-arm64v8
		docker push bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-arm32v7
		
		docker manifest create bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION \
			--amend bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-amd64 \
			--amend bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-arm64v8 \
			--amend bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-arm32v7
		docker manifest push bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION
		
		docker manifest create bayrell/$IMAGE_NAME:$VERSION \
			--amend bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-amd64 \
			--amend bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-arm64v8 \
			--amend bayrell/$IMAGE_NAME:$VERSION-$SUBVERSION-arm32v7
		docker manifest push bayrell/$IMAGE_NAME:$VERSION
	;;
	
	all)
		$0 amd64
		$0 arm64v8
		$0 arm32v7
		$0 manifest
	;;
	
	*)
		echo "Usage: $0 {amd64|arm64v8|arm32v7|manifest|all|test}"
		RETVAL=1

esac

exit $RETVAL

