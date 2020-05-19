FROM php:apache

ENV DEBIAN_FRONTEND=noninteractive

# Install dependencies
RUN apt-get update && apt-get install -y \
    # required for `apt-key add` with .gpg keys
    gnupg

# Install New Relic
ADD https://download.newrelic.com/548C16BF.gpg ./
RUN echo 'deb http://apt.newrelic.com/debian/ newrelic non-free' | tee /etc/apt/sources.list.d/newrelic.list \
    && apt-key add 548C16BF.gpg \
    && apt-get update \
    && apt-get -y install newrelic-php5 \
    && NR_INSTALL_SILENT=true newrelic-install install

# Configure New Relic
RUN echo 'extension = "newrelic.so"' > /usr/local/etc/php/conf.d/newrelic.ini \
    && echo '[newrelic]' >> /usr/local/etc/php/conf.d/newrelic.ini \
    && echo "newrelic.enabled = \${NEW_RELIC_ENABLED}" >> /usr/local/etc/php/conf.d/newrelic.ini \
    && echo "newrelic.license = \${NEW_RELIC_LICENSE_KEY}" >> /usr/local/etc/php/conf.d/newrelic.ini \
    && echo "newrelic.appname = \${NEW_RELIC_APP_NAME}" >> /usr/local/etc/php/conf.d/newrelic.ini \
    && echo "newrelic.attributes.include = request.parameters.*" >> /usr/local/etc/php/conf.d/newrelic.ini \
    && echo "newrelic.distributed_tracing_enabled = true" >> /usr/local/etc/php/conf.d/newrelic.ini
