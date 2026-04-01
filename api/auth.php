<?php
require_once '../includes/response.php';
errorResponse('Deprecated endpoint. Use /api/login.php, /api/register.php, /api/status.php, /api/logout.php', 410);
