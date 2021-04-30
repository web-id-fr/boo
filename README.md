![Boo](https://github.com/web-id-fr/boo/workflows/Boo/badge.svg?branch=main)

# 👻 Boo

Backup, Optimize and Oversee.

## Requirements

* PHP 7.4+

## Setup

```bash
git clone
make install
```

## Usage

@TODO

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