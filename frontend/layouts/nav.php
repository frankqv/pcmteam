<?php
function renderMenu($items)
{
    echo '<ul class="list-unstyled components">';
    foreach ($items as $item) {
        if (isset($item['children'])) {
            // Menú desplegable con hijos
            $menu_id = isset($item['id']) ? $item['id'] : 'menu_' . uniqid();
            // Usar # como URL por defecto para elementos con submenús
            $item_url = isset($item['url']) ? $item['url'] : '#';
            echo '<li class="dropdown">
                    <a href="' . $item_url . '" data-toggle="collapse" data-target="#' . $menu_id . '" aria-expanded="false" class="dropdown-toggle">
                        <i class="material-icons">' . $item['icon'] . '</i><span>' . $item['label'] . '</span>
                    </a>
                    <ul class="collapse list-unstyled menu" id="' . $menu_id . '">';
            renderMenu($item['children']); // Recursividad
            echo '</ul></li>';
        } else {
            // Opción normal - verificar si tiene URL
            $item_url = isset($item['url']) ? $item['url'] : '#';
            $disabled_class = isset($item['disabled']) && $item['disabled'] ? 'disabled' : '';
            echo '<li class="' . $disabled_class . '">
                    <a href="' . $item_url . '">
                        ' . (isset($item['icon']) ? '<i class="material-icons">' . $item['icon'] . '</i>' : '') . '
                        <span>' . $item['label'] . '</span>
                    </a>
                  </li>';
        }
    }
    echo '</ul>';
}
?>