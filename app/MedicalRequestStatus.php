<?php

namespace App;

enum MedicalRequestStatus: string
{
    case PENDING = 'pending';
    case RELEASE = 'release';
}
