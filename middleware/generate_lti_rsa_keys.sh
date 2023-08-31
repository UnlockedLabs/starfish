#!/bin/bash

if grep -q "LTI13_RSA_PRIVATE_KEY" .env; then
    echo "Your Key pair is already generated and in your .env file"
    exit 0
else
    if ! command -v openssl &>/dev/null; then
        echo "Error: OpenSSL is not installed."
        exit 1
    fi

    openssl genrsa -out keypair.pem 2048

    # just to be sure
    sleep 1

    openssl rsa -in keypair.pem -pubout -out public_key.pem
    private_key=$(cat keypair.pem)
    public_key=$(cat public_key.pem)

    echo "LTI13_RSA_PRIVATE_KEY=\"$private_key\"" >>.env
    echo "LTI13_RSA_PUBLIC_KEY=\"$public_key\"" >>.env

    rm keypair.pem
    rm public_key.pem
fi
