# Automated tasks for Open Air

This piece of software will stop you from being hurt by Open Air and its wonderful user experience.

## Implementation

This app is written in `PHP7`, using `Selenium` and `Mink` (PHP Driver for Selenium).
It's also wrapped in Docker, which makes it super easy to use.

Docker installation instructions can be found here:
https://docs.docker.com/engine/installation/#supported-platforms

## Global configuration

Rename `config/defaults.sample.yml` to `config/defaults.yml` and change/add based on your needs.

Please note, that this app can read only plain passwords from the config file, so it's important to set read permissions only for yourself (`0600`).

## Receipts

In `receipts/ons-newport.sample.yml` you can find sample receipt that perform following actions:
- Login to OpenAir (due to global configuration file)
- Create Travel Request
- Add Travel Train fare receipt with appropriate note (note definition in global config)
- Add Accommodation - Hotel receipt with appropriate note (note definition in global config)
- Send e-mail to Back office, using your company e-mail account

## Run

First, build docker container
```bash
make
```

Run receipt
```bash
make run receipt=ons-newport
```

Should you have any further questions, please do not hesitate to contact me.

