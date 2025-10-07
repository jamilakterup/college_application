<h1 class="page-title">@yield('page-title', 'Administrator')</h1>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.college.index') }}" class="icon wb-home">Home</a></li>
    @if(isset($breadcrumb))
        @php
            
            $breadcrumbs = explode('|', $breadcrumb);

            $items_bread = count($breadcrumbs);
            $current_bread = 0;

            foreach($breadcrumbs as $breadcrumb) {

                $pos = strpos($breadcrumb, ':');

                if($pos !== false) :
                    $text = substr($breadcrumb, $pos+1);
                    $route = substr($breadcrumb, 0, $pos);
                else :
                    $text = $breadcrumb;
                endif; 

                /**
                 * check last breadcrumb
                 */
                if(++$current_bread === $items_bread) :
                    echo "<li class='breadcrumb-item active'>$text</li>";
                else :
                    echo "<li class='breadcrumb-item'>".link_to_route($route, $text,null)."</li>";
                endif; 

            } 
        @endphp
    @endif
</ol>

