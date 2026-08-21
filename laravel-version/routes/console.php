<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('media:clean')->daily();
