FROM ubuntu:18.04 AS builder

WORKDIR /dist

RUN apt-get -y install && apt-get update
RUN apt-get -y install unzip

#download and unzip the GiveWP plugin
ADD https://downloads.wordpress.org/plugin/give.latest-stable.zip /dist
RUN cd /dist && unzip give.latest-stable.zip && rm give.latest-stable.zip

FROM wordpress:php7.2

WORKDIR /var/www/html/

#copy the paystack plugin to the wordpress plugin directory
COPY . ./wp-content/plugins/paystack

#copy the GiveWP plugin to the wordpress plugin directory
COPY --from=builder /dist/ ./wp-content/plugins
