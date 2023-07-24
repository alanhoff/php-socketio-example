FROM ubuntu:22.04

# Copy source to container for sake of build
ENV BUILD_DIR=/tmp/guacamole-server

RUN apt-get update \
  && apt-get install -y git nano \
  && git clone https://github.com/apache/guacamole-server.git ${BUILD_DIR} \
  && cd ${BUILD_DIR} \
  && git checkout 1.5.2

#
# Base directory for installed build artifacts.
#
# NOTE: Due to limitations of the Docker image build process, this value is
# duplicated in an ENV in the second stage of the build.
#
ENV PREFIX_DIR=/opt/guacamole

#
# Automatically select the latest versions of each core protocol support
# library (these can be overridden at build time if a specific version is
# needed)
#
ENV WITH_FREERDP='2(\.\d+)+'
ENV WITH_LIBSSH2='libssh2-\d+(\.\d+)+'
ENV WITH_LIBTELNET='\d+(\.\d+)+'
ENV WITH_LIBVNCCLIENT='LibVNCServer-\d+(\.\d+)+'
ENV WITH_LIBWEBSOCKETS='v\d+(\.\d+)+'

#
# Default build options for each core protocol support library, as well as
# guacamole-server itself (these can be overridden at build time if different
# options are needed)
#
# NOTA: Precisei desabilitar -DWITH_SSE2=ON para funcionar no MacBook
ENV FREERDP_OPTS="\
    -DBUILTIN_CHANNELS=OFF \
    -DCHANNEL_URBDRC=OFF \
    -DWITH_ALSA=OFF \
    -DWITH_CAIRO=ON \
    -DWITH_CHANNELS=ON \
    -DWITH_CLIENT=ON \
    -DWITH_CUPS=OFF \
    -DWITH_DIRECTFB=OFF \
    -DWITH_FFMPEG=OFF \
    -DWITH_GSM=OFF \
    -DWITH_GSSAPI=OFF \
    -DWITH_IPP=OFF \
    -DWITH_JPEG=ON \
    -DWITH_LIBSYSTEMD=OFF \
    -DWITH_MANPAGES=OFF \
    -DWITH_OPENH264=OFF \
    -DWITH_OPENSSL=ON \
    -DWITH_OSS=OFF \
    -DWITH_PCSC=OFF \
    -DWITH_PULSE=OFF \
    -DWITH_SERVER=OFF \
    -DWITH_SERVER_INTERFACE=OFF \
    -DWITH_SHADOW_MAC=OFF \
    -DWITH_SHADOW_X11=OFF \
    -DWITH_SSE2=ON \
    -DWITH_WAYLAND=OFF \
    -DWITH_X11=OFF \
    -DWITH_X264=OFF \
    -DWITH_XCURSOR=ON \
    -DWITH_XEXT=ON \
    -DWITH_XI=OFF \
    -DWITH_XINERAMA=OFF \
    -DWITH_XKBFILE=ON \
    -DWITH_XRENDER=OFF \
    -DWITH_XTEST=OFF \
    -DWITH_XV=OFF \
    -DWITH_ZLIB=ON"

ENV GUACAMOLE_SERVER_OPTS="\
    --disable-guaclog"

ENV LIBSSH2_OPTS="\
    -DBUILD_EXAMPLES=OFF \
    -DBUILD_SHARED_LIBS=ON"

ENV LIBTELNET_OPTS="\
    --disable-static \
    --disable-util"

ENV LIBVNCCLIENT_OPTS=""

ENV LIBWEBSOCKETS_OPTS="\
    -DDISABLE_WERROR=ON \
    -DLWS_WITHOUT_SERVER=ON \
    -DLWS_WITHOUT_TESTAPPS=ON \
    -DLWS_WITHOUT_TEST_CLIENT=ON \
    -DLWS_WITHOUT_TEST_PING=ON \
    -DLWS_WITHOUT_TEST_SERVER=ON \
    -DLWS_WITHOUT_TEST_SERVER_EXTPOLL=ON \
    -DLWS_WITH_STATIC=OFF"

RUN apt-get update \
  && apt-get install -y build-essential cmake zlib1g-dev libssl-dev libjpeg-dev pkg-config libcairo-dev autoconf libtool \
  && ${BUILD_DIR}/src/guacd-docker/bin/build-all.sh