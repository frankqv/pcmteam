<?php
function getInlineStyle($class, $label = '') {
    $styles = [
        'style-triage1' => ['#e74c3c', '#c0392b', 'rgba(231,76,60,0.3)'],
        'style-control-calidad' => ['#b6b059', 'rgb(222, 217, 50)', 'rgba(255, 234, 5, 0.3)'],
        'style-mantenimiento' => ['#1abc9c', '#16a085', 'rgba(26,188,156,0.3)'],
        'style-electrico' => ['#3498db', '#2980b9', 'rgba(52,152,219,0.3)'],
        'style-estetico' => ['#2c3e50', '#34495e', 'rgba(44,62,80,0.3)'],
        'business' => ['#27ae60', '#229954', 'rgba(39,174,96,0.3)'],
        'style-triage2' => ['#f39c12', '#e67e22', 'rgba(243,156,18,0.3)'],
    ];
    if (isset($styles[$class])) {
        [$from, $to, $shadow] = $styles[$class];
        return sprintf(
            'style="background: linear-gradient(135deg, %s 0%%, %s 100%%); color: white; box-shadow: 0 4px 15px %s; transition: all 0.3s ease;"',
            $from, $to, $shadow
        );
    }
    return '';
}
function renderMenu($items) {
    echo '<ul class="list-unstyled components">';
    foreach ($items as $item) {
        $class = $item['class'] ?? '';
        $label = $item['label'] ?? '';
        $style = getInlineStyle($class, $label);
        $url = $item['url'] ?? '#';
        $icon = isset($item['icon']) ? "<i class=\"material-icons\">{$item['icon']}</i>" : '';
        
        if (isset($item['children'])) {
            $id = $item['id'] ?? 'menu_' . uniqid();
            echo "<li class=\"dropdown\">
                    <a href=\"{$url}\" data-toggle=\"collapse\" data-target=\"#{$id}\" aria-expanded=\"false\" class=\"dropdown-toggle {$class}\" data-debug=\"{$class}\"{$style}>
                        {$icon}<span>{$label}</span>
                    </a>
                    <ul class=\"collapse list-unstyled menu\" id=\"{$id}\">";
            renderMenu($item['children']);
            echo '</ul></li>';
        } else {
            $disabled = (!empty($item['disabled']) && $item['disabled']) ? 'disabled' : '';
            echo "<li class=\"{$disabled}\">
                    <a href=\"{$url}\" class=\"{$class}\" data-debug=\"{$class}\"{$style}>
                        {$icon}<span>{$label}</span>
                    </a>
                </li>";
        }
    }
    echo '</ul>';
}
?>
