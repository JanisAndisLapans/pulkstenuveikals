<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name_lv', 'quantity', 'units', 'category_id', 'name_en'];
    private $constraints = ['category_id' => 'category'];

    public function getConstraints()
    {
        return $this->constraints;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class,
            "product_category", foreignPivotKey: 'category_id', relatedPivotKey: 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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
