<?php
// Mobile Blocks
$datas = [
    [
        "code" => "header",
        "name" => "Header",
        "use_color" => 1,
        "color" => "#00377a",
        "use_background_color" => 1,
        "background_color" => "#739c03",
        "position" => 10
    ],
    [
        "code" => "subheader",
        "name" => "Subheader",
        "use_color" => 1, "color" => "#00377a",
        "use_background_color" => 1,
        "background_color" => "#739c03",
        "position" => 20
    ],
    [
        "code" => "connect_button",
        "name" => "Connect Button",
        "use_color" => 1,
        "color" => "#233799",
        "use_background_color" => 1,
        "background_color" => "#f2f2f2",
        "position" => 30
    ],
    [
        "code" => "background",
        "name" => "Background",
        "use_color" => 1,
        "color" => "#ffffff",
        "use_background_color" => 1,
        "background_color" => "#0c6ec4",
        "position" => 40
    ],
    [
        "code" => "discount",
        "name" => "Discount Zone",
        "use_color" => 1,
        "color" => "#fcfcfc",
        "use_background_color" => 1,
        "background_color" => "#739c03",
        "position" => 50
    ],
    [
        "code" => "button",
        "name" => "Button",
        "use_color" => 1,
        "color" => "#fcfcfc",
        "use_background_color" => 1,
        "background_color" => "#00377a",
        "position" => 60
    ],
    [
        "code" => "news",
        "name" => "News",
        "use_color" => 1,
        "color" => "#fcfcfc",
        "use_background_color" => 1,
        "background_color" => "#00377a",
        "position" => 70
    ],
    [
        "code" => "comments",
        "name" => "Comments",
        "use_color" => 1,
        "color" => "#ffffff",
        "use_background_color" => 1,
        "background_color" => "#4d5d8a",
        "position" => 80
    ],
    [
        "code" => "tabbar",
        "name" => "Tabbar",
        "use_color" => 1,
        "color" => "#ffffff",
        "use_background_color" => 1,
        "background_color" => "#739c03",
        "image_color" => "#ffffff",
        "position" => 90
    ]
];

foreach($datas as $data) {
    $data["type_id"] = 1;
    $block = new Template_Model_Block();
    $block
        ->setData($data)
        ->insertOrUpdate(["code", "type_id"]);
}

# Inserting categories in 'template_category' table
$categories = [
    "Arts",
    "Design",
    "Corporate",
];

foreach($categories as $category_name) {
    $data = [
        "name" => $category_name,
        "code" => preg_replace('/[&\s]+/', "_", strtolower($category_name)),
    ];

    $category = new Template_Model_Category();
    $category
        ->setData($data)
        ->insertOnce(["code"]);
}

# Listing all layouts
$layouts = [];
$layout = new Application_Model_Layout_Homepage();

foreach($layout->findAll() as $layout) {
    $layouts[$layout->getCode()] = $layout;
}

# Listings all block ids
$block_ids = [];
$block = new Template_Model_Block();
foreach($block->findAll() as $block) {
    $block_ids[$block->getCode()] = $block->getId();
}

# Inserting designs with blocks
$designs = [
    "rouse" => [
        "layout_id" => $layouts["layout_6"]->getId(),
        "name" => "Red&Co",
        "overview" => "/rouse/overview.png",
        "overview_new" => "/rouse/overview_new.png",
        "background_image" => "/../../images/templates/rouse/640x1136.jpg",
        "background_image_hd" => "/../../images/templates/rouse/1242x2208.jpg",
        "background_image_tablet" => "/../../images/templates/rouse/1536x2048.jpg",
        "background_image_landscape" => "/../../images/templates/rouse/rouse-1136-640.jpg",
        "background_image_landscape_hd" => "/../../images/templates/rouse/rouse-2208-1242.jpg",
        "background_image_landscape_tablet" => "/../../images/templates/rouse/rouse-2048-1536.jpg",
        "icon" => "/../../images/templates/rouse/180x180.png",
        "startup_image" => "/../../images/templates/rouse/640x960.png",
        "startup_image_retina" => "/../../images/templates/rouse/640x1136.jpg",
        "startup_image_iphone_6" => "/../../images/templates/rouse/750x1334.png",
        "startup_image_iphone_6_plus" => "/../../images/templates/rouse/1242x2208.jpg",
        "startup_image_ipad_retina" => "/../../images/templates/rouse/1536x2048.jpg",
        "startup_image_iphone_x" => "/../../images/templates/rouse/1125x2436.jpg",
    ],
    "bleuc" => [
        "layout_id" => $layouts["layout_5"]->getId(),
        "name" => "Blutility",
        "overview" => "/bleuc/overview.png",
        "overview_new" => "/bleuc/overview_new.png",
        "background_image" => "/../../images/templates/bleuc/640x1136.jpg",
        "background_image_hd" => "/../../images/templates/bleuc/1242x2208.jpg",
        "background_image_tablet" => "/../../images/templates/bleuc/1536x2048.jpg",
        "background_image_landscape" => "/../../images/templates/bleuc/bleuc-1136-640.jpg",
        "background_image_landscape_hd" => "/../../images/templates/bleuc/bleuc-2208-1242.jpg",
        "background_image_landscape_tablet" => "/../../images/templates/bleuc/bleuc-2048-1536.jpg",
        "icon" => "/../../images/templates/bleuc/180x180.png",
        "startup_image" => "/../../images/templates/bleuc/640x960.png",
        "startup_image_retina" => "/../../images/templates/bleuc/640x1136.jpg",
        "startup_image_iphone_6" => "/../../images/templates/bleuc/750x1334.png",
        "startup_image_iphone_6_plus" => "/../../images/templates/bleuc/1242x2208.jpg",
        "startup_image_ipad_retina" => "/../../images/templates/bleuc/1536x2048.jpg",
        "startup_image_iphone_x" => "/../../images/templates/bleuc/1125x2436.jpg",
    ],
    "colors" => [
        "layout_id" => $layouts["layout_4"]->getId(),
        "name" => "Colors",
        "overview" => "/colors/overview.png",
        "overview_new" => "/colors/overview_new.png",
        "background_image" => "/../../images/templates/colors/640x1136.jpg",
        "background_image_hd" => "/../../images/templates/colors/1242x2208.jpg",
        "background_image_tablet" => "/../../images/templates/colors/1536x2048.jpg",
        "background_image_landscape" => "/../../images/templates/colors/colors-1136-640.jpg",
        "background_image_landscape_hd" => "/../../images/templates/colors/colors-2208-1242.jpg",
        "background_image_landscape_tablet" => "/../../images/templates/colors/colors-2048-1536.jpg",
        "icon" => "/../../images/templates/colors/180x180.png",
        "startup_image" => "/../../images/templates/colors/640x960.jpg",
        "startup_image_retina" => "/../../images/templates/colors/640x1136.jpg",
        "startup_image_iphone_6" => "/../../images/templates/colors/750x1334.jpg",
        "startup_image_iphone_6_plus" => "/../../images/templates/colors/1242x2208.jpg",
        "startup_image_ipad_retina" => "/../../images/templates/colors/1536x2048.jpg",
        "startup_image_iphone_x" => "/../../images/templates/colors/1125x2436.jpg",
    ],
    "blank" => [
        "layout_id" => $layouts["layout_1"]->getId(),
        "name" => "Blank",
        "overview" => "/blank/overview.png",
        "background_image" => "/../../images/application/placeholder/no-background.jpg",
        "background_image_hd" => "/../../images/application/placeholder/no-background-hd.jpg",
        "background_image_tablet" => "/../../images/application/placeholder/no-background-tablet.jpg",
        "icon" => "/../../images/application/placeholder/no-image.png",
        "startup_image" => "/../../images/application/placeholder/no-startupimage.png",
        "startup_image_retina" => "/../../images/application/placeholder/no-startupimage-retina.png",
        "startup_image_iphone_6" => "/../../images/application/placeholder/no-startupimage-iphone-6.png",
        "startup_image_iphone_6_plus" => "/../../images/application/placeholder/no-startupimage-iphone-6-plus.png",
        "startup_image_ipad_retina" => "/../../images/application/placeholder/no-startupimage-tablet.png",
        "startup_image_iphone_x" => "/../../images/application/placeholder/no-startupimage-iphone-x.png",
    ]
];

foreach($designs as $code => $data) {
    $data["code"] = $code;

    $design = new Template_Model_Design();
    $design
        ->setData($data)
        ->insertOrUpdate(["code"]);

    if (!empty($data["blocks"])) {
        foreach ($data["blocks"] as $block_code => $block_data) {

            $block_data["design_id"] = $design->getId();
            $block_data["block_id"] = $block_ids[$block_code];

            $design_block = new Template_Model_Design_Block();
            $design_block
                ->setData($block_data)
                ->insertOrUpdate(["design_id", "block_id"]);
        }
    }

}

# Assigning designs to categories
$categories_designs = [
    "design" => [
        "rouse"
    ],
    "corporate" => [
        "bleuc"
    ],
    "arts" => [
        "colors"
    ],
];

# Listing all design ids
$design_ids = [];
$design = new Template_Model_Design();
foreach ($design->findAll() as $design_data) {
    $design_ids[$design_data->getCode()] = $design_data->getId();
}

# Listing all category ids
$category_ids = [];
$category = new Template_Model_Category();
foreach ($category->findAll() as $category_data) {
    $category_ids[$category_data->getCode()] = $category_data->getId();
}

foreach ($categories_designs as $category_code => $design_codes) {
    $categories_designs_data = ["category_id" => $category_ids[$category_code]];

    foreach ($design_codes as $design_code) {
        $categories_designs_data["design_id"] = $design_ids[$design_code];

        $design_category = new Template_Model_Design_Category();
        $design_category
                ->setData($categories_designs_data)
                ->insertOrUpdate(["design_id", "category_id"]);
    }

}

# Assigning features to designs
$design_codes = [
    "rouse" => [
       "set_meal" => ["icon" => "/set_meal/meat1-flat.png"],
       "booking" => ["icon" => "/booking/booking1-flat.png"],
       "catalog" => ["icon" => "/catalog/catalog1-flat.png"],
       "discount" => ["icon" => "/discount/discount1-flat.png"],
       "loyalty" => ["icon" => "/loyalty/loyalty1-flat.png"]
    ],
    "bleuc" => [
        "facebook" => ["icon" => "/social_facebook/facebook1-flat.png"],
        "weblink_multi" => ["name" => "Links", "icon" => "/weblink/link1-flat.png"],
        "push_notification" => ["icon" => "/push_notifications/push1-flat.png"],
        "tip" => ["icon" => "/tip/tip1-flat.png"]
    ],
    "colors" => [
       "music_gallery" => ["icon" => "/musics/music1-flat.png"],
       "image_gallery" => ["icon" => "/images/image1-flat.png"],
       "video_gallery" => ["icon" => "/videos/video1-flat.png"],
       "fanwall" => ["icon" => "/fanwall/fanwall1-flat.png"],
       "radio" => ["icon" => "/radio/radio1-flat.png"],
       "calendar" => ["icon" => "/calendar/calendar1-flat.png"],
       "newswall" => ["icon" => "/newswall/newswall1-flat.png"],
       "code_scan" => ["icon" => "/code_scan/scan1-flat.png"]
    ],
];

foreach ($design_codes as $design_code => $option_codes) {
    foreach ($option_codes as $option_code => $option_infos) {

        $design = new Template_Model_Design();
        $design->find($design_code, "code");

        $option = new Application_Model_Option();
        $options = $option->findAll(["code IN (?)" => $option_code]);

        foreach($options as $option) {

            $icon_id = NULL;
            if(isset($option_infos["icon"])) {
                $icon = new Media_Model_Library_Image();
                $icon->find($option_infos["icon"], "link");

                if (!$icon->getData()) {
                    $icon
                        ->setLibraryId($option->getLibraryId())
                        ->setLink($option_infos["icon"])
                        ->setOptionId($option->getId())
                        ->setCanBeColorized(1)
                        ->setPosition(0)
                        ->save()
                    ;
                }

                $icon_id = $icon->getId();
            }

            $data = [
                "design_id" => $design->getId(),
                "option_id" => $option->getId(),
                "option_tabbar_name" => isset($option_infos["name"]) ? $option_infos["name"] : NULL,
                "option_icon" => $icon_id,
                "option_background_image" => isset($option_infos["background_image"]) ? $option_infos["background_image"] : NULL
            ];

            $design_content = new Template_Model_Design_Content();
            $design_content
                ->setData($data)
                ->insertOrUpdate(["design_id", "option_id"]);

        }
    }
}

$blocks = [
    /* GENERAL */
    [
        "code" => "background",
        "name" => "General",
        "background_color" => "#ededed",
        "background_color_variable_name" => '$general-custom-bg',
        "position" => "10"
    ],
    /* HEADER */
    [
        "code" => "header",
        "name" => "Header",
        "color" => "#444",
        "color_variable_name" => '$bar-custom-text',
        "background_color" => "#f8f8f8",
        "background_color_variable_name" => '$bar-custom-bg',
        "border_color" => "#b2b2b2",
        "border_color_variable_name" => '$bar-custom-border',
        "position" => "20"
    ],
    /* HOMEPAGE */
    [
        "code" => "homepage",
        "name" => "Homepage",
        "color" => "#111",
        "color_variable_name" => '$homepage-custom-text',
        "background_color" => "#fff",
        "background_color_variable_name" => '$homepage-custom-bg',
        "border_color" => "#ddd",
        "border_color_variable_name" => '$homepage-custom-border',
        "image_color" => "#ddd",
        "image_color_variable_name" => '$homepage-custom-image',
        "position" => "30"
    ],
    /* LIST */
    [
        "code" => "list",
        "name" => "List",
        "position" => "50",
        "children" => [
            [
                "code" => "list_item_divider",
                "name" => "Title's List",
                "color" => "#222",
                "color_variable_name" => '$list-item-divider-custom-text',
                "background_color" => "#f8f8f8",
                "background_color_variable_name" => '$list-item-divider-custom-bg'
            ], [
                "code" => "list_item",
                "name" => "List Item",
                "color" => "#444",
                "color_variable_name" => '$list-item-custom-text',
                "background_color" => "#fff",
                "background_color_variable_name" => '$list-item-custom-bg'
            ]
        ]
    ],
    /* CARD */
    [
        "code" => "card",
        "name" => "Card",
        "position" => "70",
        "children" => [
            [
                "code" => "card_item_divider",
                "name" => "Title's Card",
                "color" => "#222",
                "color_variable_name" => '$card-item-divider-custom-text',
                "background_color" => "#f8f8f8",
                "background_color_variable_name" => '$card-item-divider-custom-bg'
            ], [
                "code" => "card_item",
                "name" => "Card Item",
                "color" => "#444",
                "color_variable_name" => '$card-item-custom-text',
                "background_color" => "#fff",
                "background_color_variable_name" => '$card-item-custom-bg'
            ]
        ]
    ],
    /* BUTTONS */
    [
        "code" => "buttons_group",
        "name" => "Buttons",
        "position" => "80",
        "children" => [
            [
                "code" => "buttons",
                "name" => "Button",
                "more" => "phone, locate, facebook, email, etc..",
                "color" => "#444",
                "color_variable_name" => '$button-custom-text',
                "background_color" => "#f8f8f8",
                "background_color_variable_name" => '$button-custom-bg',
                "border_color" => "#b2b2b2",
                "border_color_variable_name" => '$button-custom-border',
            ],
            [
                "code" => "buttons_light",
                "name" => "Button light",
                "color" => "#444",
                "color_variable_name" => '$button-light-custom-text',
                "background_color" => "#ffffff",
                "background_color_variable_name" => '$button-light-custom-bg',
                "border_color" => "#dddddd",
                "border_color_variable_name" => '$button-light-custom-border',
            ],
            [
                "code" => "buttons_positive",
                "name" => "Button positive",
                "more" => "form submit, search, validation, confirmation",
                "color" => "#ffffff",
                "color_variable_name" => '$button-positive-custom-text',
                "background_color" => "#387ef5",
                "background_color_variable_name" => '$button-positive-custom-bg',
                "border_color" => "#0c60ee",
                "border_color_variable_name" => '$button-positive-custom-border',
            ],
            [
                "code" => "buttons_calm",
                "name" => "Button calm",
                "more" => "informative, modal",
                "color" => "#ffffff",
                "color_variable_name" => '$button-calm-custom-text',
                "background_color" => "#11c1f3",
                "background_color_variable_name" => '$button-calm-custom-bg',
                "border_color" => "#0a9dc7",
                "border_color_variable_name" => '$button-calm-custom-border',
            ],
            [
                "code" => "buttons_balanced",
                "name" => "Button balanced",
                "more" => "contextual, depends on module/layout",
                "color" => "#ffffff",
                "color_variable_name" => '$button-balanced-custom-text',
                "background_color" => "#33cd5f",
                "background_color_variable_name" => '$button-balanced-custom-bg',
                "border_color" => "#28a54c",
                "border_color_variable_name" => '$button-balanced-custom-border',
            ],
            [
                "code" => "buttons_energized",
                "name" => "Button energized",
                "more" => "contextual, depends on module/layout",
                "color" => "#ffffff",
                "color_variable_name" => '$button-energized-custom-text',
                "background_color" => "#ffc900",
                "background_color_variable_name" => '$button-energized-custom-bg',
                "border_color" => "#e6b500",
                "border_color_variable_name" => '$button-energized-custom-border',
            ],
            [
                "code" => "buttons_assertive",
                "name" => "Button assertive",
                "more" => "confirm action, deletion, etc ...",
                "color" => "#ffffff",
                "color_variable_name" => '$button-assertive-custom-text',
                "background_color" => "#ef473a",
                "background_color_variable_name" => '$button-assertive-custom-bg',
                "border_color" => "#e42112",
                "border_color_variable_name" => '$button-assertive-custom-border',
            ],
            [
                "code" => "buttons_royal",
                "name" => "Button royal",
                "more" => "contextual, depends on module/layout",
                "color" => "#ffffff",
                "color_variable_name" => '$button-royal-custom-text',
                "background_color" => "#886aea",
                "background_color_variable_name" => '$button-royal-custom-bg',
                "border_color" => "#6b46e5",
                "border_color_variable_name" => '$button-royal-custom-border',
            ],
            [
                "code" => "buttons_dark",
                "name" => "Button dark",
                "more" => "contextual, depends on module/layout",
                "color" => "#ffffff",
                "color_variable_name" => '$button-dark-custom-text',
                "background_color" => "#444444",
                "background_color_variable_name" => '$button-dark-custom-bg',
                "border_color" => "#111111",
                "border_color_variable_name" => '$button-dark-custom-border',
            ],
        ],
    ],
    /* CHECKBOX */
    [
        "code" => "checkbox",
        "name" => "Checkbox",
        "position" => "90",
        "children" => [
            [
                "code" => "checkbox_general",
                "name" => "General",
                "background_color" => "#fff",
                "background_color_variable_name" => '$checkbox-general-custom-bg',
                "color" => "#444",
                "color_variable_name" => '$checkbox-general-custom-text'
            ], [
                "code" => "checkbox_on",
                "name" => "Checkbox on",
                "background_color" => "#387ef5",
                "background_color_variable_name" => '$checkbox-on-custom-bg',
                "color" => "#fff",
                "color_variable_name" => '$checkbox-on-custom-text'
            ], [
                "code" => "checkbox_off",
                "name" => "Checkbox off",
                "background_color" => "#fff",
                "background_color_variable_name" => '$checkbox-off-custom-bg'
            ],
        ]
    ],
    /* RADIO */
    [
        "code" => "radio",
        "name" => "Radio",
        "color" => "#444",
        "color_variable_name" => '$radio-custom-text',
        "background_color" => "#fff",
        "background_color_variable_name" => '$radio-custom-bg',
        "position" => "100"
    ],
    /* TOGGLE */
    [
        "code" => "toggle",
        "name" => "Toggle",
        "position" => "110",
        "children" => [
            [
                "code" => "toggle_general",
                "name" => "General",
                "color" => "#444",
                "color_variable_name" => '$toggle-general-custom-text',
                "background_color" => "#fff",
                "background_color_variable_name" => '$toggle-general-custom-bg'
            ], [
                "code" => "toggle_on",
                "name" => "Toggle on",
                "background_color" => "#387ef5",
                "background_color_variable_name" => '$toggle-on-custom-bg'
            ], [
                "code" => "toggle_off",
                "name" => "Toggle off",
                "background_color" => "#fff",
                "background_color_variable_name" => '$toggle-off-custom-bg',
                "border_color" => "#e6e6e6",
                "border_color_variable_name" => '$toggle-off-custom-border'
            ], [
                "code" => "toggle_handle_on",
                "name" => "Toggle's Handle on",
                "background_color" => "#fff",
                "background_color_variable_name" => '$toggle-handle-on-custom-bg'
            ], [
                "code" => "toggle_handle_off",
                "name" => "Toggle's Handle off",
                "background_color" => "#fff",
                "background_color_variable_name" => '$toggle-handle-off-custom-bg'
            ],
        ]
    ],
    /* TOOLTIP */
    [
        "code" => "tooltip",
        "name" => "Tooltip",
        "color" => "#fff",
        "color_variable_name" => '$tooltip-custom-text',
        "background_color" => "#444",
        "background_color_variable_name" => '$tooltip-custom-bg',
        "position" => "120"
    ],
    /* ICON */
    [
        "code" => "icons",
        "name" => "Icons",
        "position" => "125",
        "children" => [
            [
                "code" => "icon",
                "name" => "Icon",
                "color" => "#fff",
                "color_variable_name" => '$icon-custom',
            ], [
                "code" => "icon_active",
                "name" => "Icon active",
                "color" => "#333",
                "color_variable_name" => '$icon-active-custom',
            ], [
                "code" => "icon_inactive",
                "name" => "Icon inactive",
                "color" => "#cccccc",
                "color_variable_name" => '$icon-inactive-custom',
            ],
        ]
    ],
    /* SPINNER */
    [
        "code" => "spinner",
        "name" => "Spinner",
        "position" => "130",
        "children" => [
            [
                "code" => "spinner_ios_text",
                "name" => "iOS Spinner",
                "background_color" => "#69717d",
                "background_color_variable_name" => '$spinner-custom-ios-bg'
            ], [
                "code" => "spinner_android_text",
                "name" => "Android Spinner",
                "background_color" => "#4b8bf4",
                "background_color_variable_name" => '$spinner-custom-android-bg'
            ],
        ]
    ],
    /* DIALOG */
    [
        "code" => "dialog",
        "name" => "Dialog",
        "position" => "135",
        "children" => [
            [
                "code" => "dialog_text",
                "name" => "Dialog text",
                "color" => "#000",
                "color_variable_name" => '$dialog-custom-text',
            ], [
                "code" => "dialog_bg",
                "name" => "Dialog background",
                "color" => "#fff",
                "color_variable_name" => '$dialog-custom-bg',
            ], [
                "code" => "dialog_button",
                "name" => "Dialog button",
                "color" => "#007aff",
                "color_variable_name" => '$dialog-custom-button',
            ],
        ]
    ]
];


foreach($blocks as $data) {

    $data["type_id"] = 3;
    $block = new Template_Model_Block();
    $block
        ->setData($data)
        ->insertOrUpdate(["code", "type_id"]);

    if(!empty($data["children"])) {

        $position = $block->getPosition();
        foreach($data["children"] as $child_data) {

            $position += 2;
            $child_data["type_id"] = 3;
            $child_data["parent_id"] = $block->getId();
            $child_data["position"] = $position;
            $child = new Template_Model_Block();
            $child
                ->setData($child_data)
                ->insertOrUpdate(["code", "type_id"]);

        }
    }
}

# Listing all layouts
$layouts = [];
$layout = new Application_Model_Layout_Homepage();

foreach($layout->findAll() as $layout) {
    $layouts[$layout->getCode()] = $layout;
}

# Listings all block ids
$block_ids = [];
$blocks = new Template_Model_Block();

foreach($blocks->findAll() as $block) {
    $block_ids[$block->getCode()] = $block->getId();
    $children = $block->getChildren() ? $block->getChildren() : [$block];
    foreach($children as $child) {
        $block_ids[$child->getCode()] = $child->getId();
    }
}

# Inserting designs with blocks
$designs = [
    "rouse" => [
        "layout_id" => $layouts["layout_6"]->getId(),
        "name" => "Rouse",
        "blocks" => [
            "header" => [
                "color" => "#ffffff",
                "background_color" => "#EE4B63",
                "border_color" => "#ffffff"
            ],
            "buttons_positive" => [
                "color" => "#ffffff",
                "background_color" => "#EE4B63",
                "border_color" => "#ffffff",
            ],
            "subheader" => [
                "color" => "#ffffff",
                "background_color" => "#EE4B63",
                "border_color" => "#ffffff"
            ],
            "homepage" => [
                "color" => "#ffffff",
                "background_color" => "#ffffff",
                "background_opacity" => 20,
                "border_color" => "#ffffff",
                "border_opacity" => 100,
                "image_color" => "#ffffff"
            ],
            "background" => [
                "background_color" => "#242037"
            ],
            "list_item_divider" => [
                "color" => "#ffffff",
                "background_color" => "#EE4B63"
            ],
            "list_item" => [
                "color" => "#000222",
                "background_color" => "#ffffff"
            ],
            "card_item_divider" => [
                "color" => "#000222",
                "background_color" => "#ee4b63"
            ],
            "checkbox_on" => [
                "color" => "#ffffff",
                "background_color" => "#ee4b63"
            ],
            "toggle_on" => [
                "background_color" => "#ee4b63"
            ],
            "spinner_android_text" => [
                "background_color" => "#ee4b63"
            ],
        ],
    ],
    "bleuc" => [
        "layout_id" => $layouts["layout_5"]->getId(),
        "name" => "Bleuc",
        "blocks" => [
            "header" => [
                "color" => "#ffffff",
                "background_color" => "#1374CE",
                "border_color" => "#ffffff",
            ],
            "buttons_positive" => [
                "color" => "#ffffff",
                "background_color" => "#1374CE",
                "border_color" => "#ffffff",
            ],
            "subheader" => [
                "color" => "#ffffff",
                "background_color" => "#1374CE",
                "border_color" => "#ffffff"
            ],
            "homepage" => [
                "color" => "#ffffff",
                "background_color" => "#1374CE",
                "background_opacity" => 20,
                "border_color" => "#ffffff",
                "border_opacity" => 0,
                "image_color" => "#ffffff"
            ],
            "background" => [
                "background_color" => "#ffffff"
            ],
            "list_item_divider" => [
                "color" => "#ffffff",
                "background_color" => "#1374CE"
            ],
            "list_item" => [
                "color" => "#000222",
                "background_color" => "#ffffff"
            ],
            "card_item_divider" => [
                "color" => "#000222",
                "background_color" => "#1374CE"
            ],
            "checkbox_on" => [
                "color" => "#ffffff",
                "background_color" => "#1374CE"
            ],
            "toggle_on" => [
                "background_color" => "#1374CE"
            ],
            "spinner_android_text" => [
                "background_color" => "#1374CE"
            ],
        ],
    ],
    "colors" => [
        "layout_id" => $layouts["layout_4"]->getId(),
        "name" => "Colors",
        "blocks" => [
            "header" => [
                "color" => "#ffffff",
                "background_color" => "#ee4b63",
                "border_color" => "#ffffff"
            ],
            "buttons_positive" => [
                "color" => "#ffffff",
                "background_color" => "#ee4b63",
                "border_color" => "#ffffff"
            ],
            "subheader" => [
                "color" => "#ffffff",
                "background_color" => "#ee4b63",
                "border_color" => "#ffffff"
            ],
            "homepage" => [
                "color" => "#0faca4",
                "background_color" => "#0faca4",
                "border_color" => "#0faca4",
                "image_color" => "#ffffff"
            ],
            "background" => [
                "background_color" => "#0faca4"
            ],
            "list_item_divider" => [
                "color" => "#ffffff",
                "background_color" => "#0faca4"
            ],
            "list_item" => [
                "color" => "#000222",
                "background_color" => "#ffffff"
            ],
            "card_item_divider" => [
                "color" => "#ffffff",
                "background_color" => "#0faca4"
            ],
            "checkbox_on" => [
                "color" => "#ffffff",
                "background_color" => "#0faca4"
            ],
            "toggle_on" => [
                "background_color" => "#0faca4"
            ],
            "spinner_android_text" => [
                "background_color" => "#0faca4"
            ],
        ]
    ]
];


foreach($designs as $code => $data) {
    $design = new Template_Model_Design();
    $design->find($code, "code");

    if($design->getId()) {
        if (!empty($data["blocks"])) {
            foreach ($data["blocks"] as $block_code => $block_data) {
                $block_data["design_id"] = $design->getId();
                $block_data["block_id"] = $block_ids[$block_code];

                $template_block = new Template_Model_Design_Block();
                $template_block
                    ->setData($block_data)
                    ->insertOrUpdate(["design_id", "block_id"]);
            }
        }
    }
}
