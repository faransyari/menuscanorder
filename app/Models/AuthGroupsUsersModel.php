<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthGroupsUsersModel extends Model
{
    protected $table = 'auth_groups_users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'group'];
    protected $returnType = 'object';
    protected $useTimestamps = false; // Update if timestamps are used in the table
}
