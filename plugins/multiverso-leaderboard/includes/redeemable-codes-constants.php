<?php

const REDEEMABLE_CODES_VERSION = '1.0.0';
const REDEEMABLE_CODE_CODES_TABLE_NAME = 'redeemable_codes_codes';
const REDEEMABLE_CODE_ORIGINS_TABLE_NAME = 'redeemable_codes_allowed_origins';
const REDEEMABLE_CODE_EXPIRATION_DAYS = 30;
const REDEEMABLE_CODE_RATE_LIMIT_SECONDS = 60;
const REDEEMABLE_CODE_RATE_LIMIT_REQUESTS = 5;
const REDEEMABLE_CODE_CORS_ALLOWED_METHODS = "GET, PATCH, OPTIONS";
const REDEEMABLE_CODE_CODE_GEN_MAX_RETRIES = 10;
