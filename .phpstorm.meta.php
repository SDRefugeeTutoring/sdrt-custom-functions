<?php

namespace PHPSTORM_META {
    // Allow PhpStorm IDE to resolve return types when calling sdrt( Object_Type::class ) or sdrt( `Object_Type` ).
    override(
        \sdrt( 0 ),
        map( [
            '' => '@',
            '' => '@Class',
        ] )
    );
}
