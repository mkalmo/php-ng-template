# AngularJs like templates for PHP

Loading the library and rendering a template.

```
require_once 'lib/tpl.php';

$data = [
  'greeting' => 'Hello!',
  'flag' => false,  
  'value' => 1,  
  'list' => [1, 2, 3],
  'path' => 'sub.html'  
];

print renderTemplate('tpl/main.html', $data);
```

Method renderTemplate() takes a dictionary with translations as an optional third argument.

```

$translations = ['lang-en' => 'English'];

print renderTemplate('tpl/main.html', [], $translations);
```


## Template syntax

### Simple variable substitution

```
{{ $greeting }}
```

Will render:

```
Hello!
```

### Using functions

```
{{ join(', ', $list) }}
```

Will render:

```
1, 2, 3
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

### tpl tag is removed from output

```
a<tpl tpl-if="!$flag">b</tpl>c
```

Will render:

```
abc
```

### tpl-trim-contents attribute allows source code indentation

```
<tpl tpl-foreach="[1, 2, 3] as $i" tpl-trim-contents>
    {{ $i }}
</tpl>
```

Will render:

```
123
```

### In foreach body there are special variables $first and $last 

```
<tpl tpl-foreach="[1, 2, 3] as $each" tpl-trim-contents>
    {{ $each }}<tpl tpl-if="!$last">, </tpl>
</tpl>
```

Will render:

```
1, 2, 3
```

### tpl-class provides conditional css classes

```
<div class="a" tpl-class="b if $value > 0"></div>
```

Will render:

```
<div class="a b"></div>
```

### tpl-checked is for marking checkboxes and radios checked

```
<input type="radio" tpl-checked="$value > 0">
```

Will render:

```
<input checked="checked" type="radio">
```

### tpl-selected is for marking options selected

```
<option tpl-selected="$value > 0">Option 1</option>
```

Will render:

```
<option selected="selected">Option 1</option>
```

### tpl-disabled is for marking elements disabled

```
<input tpl-disabled="$condition" />
```

Will render:

```
<input disabled="disabled" />
```

if $condition was true

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

### Translations

Translation keys have different syntax (without $ symbol).

Translation key must start with a letter or underscore and can contain 
letters, underscores or hyphens.

```
{{ lang-en }}
```

Will render:

```
English
```

## Build single bundle from the source

```
php build.php
```

produces single file

```
./dist/tpl.php
```
