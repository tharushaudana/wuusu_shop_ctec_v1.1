@if (!defined('TREEVIEW_FUNCTIONS_DECLARED'))
    @php
        function generateNested($data) 
        {
            $nested = '';

            foreach ($data as $k => $v) {
                $name = resolveKeyName($k);

                if (is_array($v)) {
                    $nested .= '
                        <li>
                            <span class="child caret">'.(is_assoc($data) ? $name : '').'</span>
                            <ul class="nested">
                    ';
                    $nested .= generateNested($v);
                    $nested .= '
                            </ul>
                        </li>
                    ';
                } else {
                    $nested .= '<li><span style="font-weight: bold;">'.(is_assoc($data) ? $name.':&nbsp;&nbsp;' : '').'</span><span style="font-family: monospace; color: #1e90ff; border: 1px dashed #aaa;">'.$v.'</span></li>';
                }
            }

            return $nested;
        }

        function resolveKeyName($kname) {
            $arr = explode('_', $kname);
            
            foreach ($arr as $i => $v) {
                $arr[$i] = ucfirst($v);
            }
            
            return implode('', $arr);
        }

        function is_assoc(array $arr)
        {
            if (array() === $arr) return false;
            return array_keys($arr) !== range(0, count($arr) - 1);
        }

        define('TREEVIEW_FUNCTIONS_DECLARED', null);
    @endphp
@endif

@php
    $content = generateNested($data);
@endphp

<style>
    .tree, .tree ul {
        list-style-type: none;
    }

    .tree {
        margin: 0;
        padding: 0;
        /*font-family: 'monospace';*/
    }

    .tree .caret {
        color: black;
        font-weight: bold;
        cursor: pointer;
        -webkit-user-select: none; /* Safari 3.1+ */
        -moz-user-select: none; /* Firefox 2+ */
        -ms-user-select: none; /* IE 10+ */
        user-select: none;
    }

    .tree .caret::before {
        content: "\25B6";
        color: black;
        display: inline-block;
        margin-right: 6px;
    }

    .tree .caret-down::before {
        -ms-transform: rotate(90deg); /* IE 9 */
        -webkit-transform: rotate(90deg); /* Safari */'
        transform: rotate(90deg);  
    }

    .tree .nested {
        display: none;
    }

    .tree .active {
        display: block;
    }
</style>

<ul class="tree">
    <li>
        <span class="caret caret-down" style="font-size: 20px;">{!! $topname !!}</span>
        <ul class="active">
            {!! $content !!}  
        </ul>
    </li>
</ul>

<script>
    var toggler = document.getElementsByClassName("child caret");
    
    var i;

    for (i = 0; i < toggler.length; i++) {
        toggler[i].addEventListener("click", function() {
            this.parentElement.querySelector(".nested").classList.toggle("active");
            this.classList.toggle("caret-down");
        });
    }
</script>