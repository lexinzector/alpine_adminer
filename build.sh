#!/bin/bash

SCRIPT=$(readlink -f "$0")
SCRIPT_PATH=$(dirname "$SCRIPT")
BASE_PATH=$(dirname "$SCRIPT_PATH")

RETVAL=0
VERSION=5.0.6
SUBVERSION=1
IMAGE_NAME="alpine_adminer"
TAG=$(date '+%Y%m%d_%H%M%S')
REGISTRY="docker.io/lexinzector"

build_and_push() {
    local ARCH=$1
    local PLATFORM=$2
    local FULL_TAG="$REGISTRY/$IMAGE_NAME:$VERSION-$SUBVERSION-$ARCH"

    echo "Building for $ARCH ($PLATFORM)..."
    docker buildx build --platform "$PLATFORM" -t "$FULL_TAG" --file Dockerfile . --push
}

create_manifest() {
    echo "Creating and pushing multi-arch manifest..."

    docker buildx imagetools create -t "$REGISTRY/$IMAGE_NAME:$VERSION-$SUBVERSION" \
        "$REGISTRY/$IMAGE_NAME:$VERSION-$SUBVERSION-amd64" \
        "$REGISTRY/$IMAGE_NAME:$VERSION-$SUBVERSION-arm64v8" \
        "$REGISTRY/$IMAGE_NAME:$VERSION-$SUBVERSION-arm32v7"

    docker buildx imagetools create -t "$REGISTRY/$IMAGE_NAME:$VERSION" \
        "$REGISTRY/$IMAGE_NAME:$VERSION-$SUBVERSION-amd64" \
        "$REGISTRY/$IMAGE_NAME:$VERSION-$SUBVERSION-arm64v8" \
        "$REGISTRY/$IMAGE_NAME:$VERSION-$SUBVERSION-arm32v7"
}

case "$1" in

    test)
        docker buildx build --platform linux/amd64 \
            -t "$REGISTRY/$IMAGE_NAME:$VERSION-$SUBVERSION-$TAG" \
            --file Dockerfile .
    ;;

    amd64)
        build_and_push "amd64" "linux/amd64"
    ;;

    arm64v8)
        build_and_push "arm64v8" "linux/arm64/v8"
    ;;

    arm32v7)
        build_and_push "arm32v7" "linux/arm/v7"
    ;;

    manifest)
        create_manifest
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
    ;;

esac

exit $RETVAL
