<?php
function renderMenu($items)
{
    echo '<ul class="list-unstyled components">';
    foreach ($items as $item) {
        if (isset($item['children'])) {
            // Menú desplegable con hijos
            echo '<li class="dropdown">
                    <a href="#' . $item['id'] . '" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="material-icons">' . $item['icon'] . '</i><span>' . $item['label'] . '</span>
                    </a>
                    <ul class="collapse list-unstyled menu" id="' . $item['id'] . '">';
            renderMenu($item['children']); // Recursividad
            echo '</ul></li>';
        } else {
            // Opción normal
            echo '<li>
                    <a href="' . $item['url'] . '">
                        ' . (isset($item['icon']) ? '<i class="material-icons">' . $item['icon'] . '</i>' : '') . '
                        <span>' . $item['label'] . '</span>
                    </a>
                  </li>';
        }
    }
    echo '</ul>';
}
?>
