<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = ['ans', 'inq_id'];

    private $constraints = ['inq_id' => 'inquiry'];

    public function getConstraints()
    {
        return $this->constraints;
    }

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function getTableColumns() {
        //Atgriež visas kolonnas tabulā ar tipiem formātā, piem.,
        //['id' => 'nofill', 'name' => 'text', 'count' => 'integer']

        $builder = $this->getConnection()->getSchemaBuilder();
        $names = $builder->getColumnListing($this->getTable());
        $type_name = [];
        foreach($names as $name)
        {
            if(!in_array($name, $this->fillable))
            {
                $type_name[$name] = 'nofill';
            }
            else {
                $type_name[$name] = $builder->getColumnType($this->getTable(), $name);
            }
        }
        return $type_name;
    }
}
