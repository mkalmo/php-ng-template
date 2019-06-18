# AngularJs like templates for PHP

Loading the library and rendering a template.

```
require_once 'lib/tpl.php';

$data = [
  'greeting' => 'Hello!',
  'flag' => false,  
  'list' => [1, 2, 3],
  'path' => 'sub.html'  
];

print render_template('tpl/main.html', $data);
```

## Template syntax

### Simple variable substitution

```
<div>{{ $greeting }}</div>
```

Will render:

```
<div>Hello!</div>
```

### Conditional content

```
a<span tpl-if="$flag">b</span>c
```

Will render:

```
ac
```

### For loop

```
<div tpl-foreach="$list as $each">{{ $each }}</div>
```

Will render:

```
<div>1</div><div>2</div><div>3</div>
```

### Includes

```
<div tpl-include="{{ $path }}"></div>
```

Will render:

```
<div>
  <!-- 
  content from sub template
  loaded from the $path -->
</div>
```
