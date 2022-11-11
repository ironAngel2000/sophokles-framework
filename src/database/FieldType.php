<?php

namespace Sophokles\Database;

enum FieldType
{
    case BIT;
    case BOOLEAN;
    case CHAR;
    case DATE;
    case DATETIME;
    case DECIMAL;
    case ENUM;
    case VARCHAR;
    case INT;
    case BIGINT;
    case JSON;
    case TIME;
    case TEXT;
    case TIMESTAMP;
    case BLOB;
}
