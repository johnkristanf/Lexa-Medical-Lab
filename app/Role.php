<?php

namespace App;

enum Role: int
{
    case ADMIN = 1;
    case MEDICAL_STAFF = 2;
    case INVENTORY_OFFICER = 3;
}
