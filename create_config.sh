#!/bin/bash
#write by 

SHELL_PATH=$(cd "$(dirname "$0")";pwd)
cd ${SHELL_PATH}

PHPCMDPATH='/data/spserver/php/bin/php'

EC2_REGION=`curl -s http://169.254.169.254/latest/dynamic/instance-identity/document | grep region | awk -F\" '{print $4}'`
case ${EC2_REGION} in
    "ap-southeast-1") region='sg';;
    "us-east-1") region='vg';;
    "us-west-1") region='california';;
    "eu-west-1") region='ireland';;
    "eu-central-1") region='frankfurt';;
    "ap-northeast-1") region='tokyo';;
    "ap-southeast-2") region='sydney';;
    "sa-east-1") region='saopaulo';;
    *) echo "no this region";;
esac


${PHPCMDPATH} ${SHELL_PATH}/artisan create:env ${region} && echo "create:env success" || echo "create:env failed"
${PHPCMDPATH} ${SHELL_PATH}/artisan permission:add && echo "permission:add success" || echo "permission:add failed"
${PHPCMDPATH} ${SHELL_PATH}/artisan cache:clear && echo "cache:clear success" || echo "cache:clear failed"
${PHPCMDPATH} ${SHELL_PATH}/artisan config:clear && echo "create:env success" || echo "config:clear failed"