![Boo](https://github.com/web-id-fr/boo/workflows/Boo/badge.svg?branch=main)

# 👻 Boo

Backup, Optimize and Oversee.

## Requirements

* PHP 7.4+
* MozJpeg (`brew install mozjpeg`)
* PngQuant (`brew install pngquant`)

## Setup

```bash
git clone
make install
cp .env.example .env
```

Update .env file with your S3 credentials if needed.

## Usage

* Download from s3 (`php bin/app s3:download-directory /dir`)
* Resize images (`php bin/app img:resize --size 1345 /dir`)
* Optimize images (`php bin/app img:optimize /dir`)
* Upload to s3 (`php bin/app s3:upload-directory /dir`)

Note : optimize should be called AFTER resize for better results.

## Troubleshooting

If you have this error message:

> Unable to open /Users/gabriel/Desktop/S3-prod/attachments/f584d67c2e2f6bb5c43c3b8d3879934d2615542e.pdf u
> sing mode r: fopen(/Users/gabriel/Desktop/S3-prod/attachments/f584d67c2e2f6bb5c43c3b8d3879934d2615542e.p
> df): failed to open stream: Too many open files

You can bypass it by setting `ulimit -S -n 6000` temporaly on your Mac.

## Tests and commands

See Makefile for all projects or commands:

```bash
make
make test
```