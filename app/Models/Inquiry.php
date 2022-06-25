<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'product_id'];
    private $constraints = ['product_id' => 'product'];

    public function getConstraints()
    {
        return $this->constraints;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function answers()
    {
        return $this->belongsToMany(Answer::class);
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
