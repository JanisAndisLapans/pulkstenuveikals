<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'desc', 'image', 'active'];

    public function orders()
    {
        return $this->belongsToMany(Order::class,
            "product_order",  foreignPivotKey: 'product_id', relatedPivotKey: 'order_id')->withPivot('count');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,
            "product_category", foreignPivotKey: 'product_id', relatedPivotKey: 'category_id');
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
            $type_name['image'] = 'img';
        }
        return $type_name;
    }

    public function reviewVerificationCodes()
    {
        return $this->belongsToMany(ReviewVerificationCode::class);
    }
}
