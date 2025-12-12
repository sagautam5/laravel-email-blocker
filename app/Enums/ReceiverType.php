<?php

namespace Sagautam5\EmailBlocker\Enums;

enum ReceiverType: string
{
    case TO = 'to';
    case CC = 'cc';
    case BCC = 'bcc';
}
