<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Widget;
use App\Models\Header;
use App\Models\Footer;

class Layout extends Model
{
    protected $fillable = ['title', 'description', 'header_id', 'footer_id'];

    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'layout_widget')
                    ->withPivot('position')
                    ->orderBy('layout_widget.position');
    }

    public function header()
    {
        return $this->belongsTo(Header::class); 
    }

    public function footer()
    {
        return $this->belongsTo(Footer::class); 
    }
}
