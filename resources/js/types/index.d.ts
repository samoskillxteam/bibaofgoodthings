export interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

export interface Category {
    id: number;
    name: string;
    slug: string;
    brand_name: string;
    subtitle?: string;
    description?: string;
    image?: string;
    banner_image?: string;
    icon?: string;
    is_active: boolean;
    display_order: number;
    products_count?: number;
}

export interface ProductMedia {
    id: number;
    product_id: number;
    file_path: string;
    url: string;
    media_type: 'image' | 'video';
    alt_text?: string;
    display_order: number;
    is_primary: boolean;
    thumbnail_path?: string;
}

export interface ProductOptionValue {
    id: number;
    product_option_id: number;
    name: string;
    price_modifier: string | number;
    color_code?: string;
    display_order: number;
}

export interface ProductOption {
    id: number;
    product_id: number;
    name: string;
    display_order: number;
    values: ProductOptionValue[];
}

export interface ProductVariant {
    id: number;
    product_id: number;
    sku?: string;
    title: string;
    price: number;
    sale_price?: number | null;
    effective_price: number;
    stock_quantity: number;
    is_available: boolean;
    option_values?: ProductOptionValue[];
}

export interface ProductAddon {
    id: number;
    product_id: number;
    name: string;
    price: number;
    is_available: boolean;
    display_order: number;
}

export interface Product {
    id: number;
    category_id: number;
    name: string;
    slug: string;
    sku?: string;
    product_type?: string;
    short_description?: string;
    full_description?: string;
    base_price: number;
    sale_price?: number | null;
    cost_price?: number | null;
    effective_price: number;
    stock_quantity: number;
    track_stock: boolean;
    is_available: boolean;
    is_featured: boolean;
    display_order: number;
    primary_image_url?: string | null;
    primary_video_url?: string | null;
    category?: Category;
    media?: ProductMedia[];
    images?: ProductMedia[];
    videos?: ProductMedia[];
    options?: ProductOption[];
    variants?: ProductVariant[];
    addons?: ProductAddon[];
    created_at?: string;
    updated_at?: string;
}

export interface CartItemAddon {
    id: number;
    name: string;
    price: number;
    formatted_price: string;
}

export interface CartItem {
    key: string;
    product_id: number;
    product_name: string;
    product_slug: string;
    category_name: string;
    category_slug: string;
    brand_name: string;
    image_url?: string | null;
    variant_id?: number | null;
    variant_title?: string | null;
    selected_options: Record<string, string>;
    addons: CartItemAddon[];
    notes?: string | null;
    quantity: number;
    unit_price: number;
    formatted_unit_price: string;
    line_total: number;
    formatted_line_total: string;
}

export interface CartData {
    items: CartItem[];
    total_quantity: number;
    subtotal: number;
    formatted_subtotal: string;
    is_empty: boolean;
    warnings: string[];
}

export interface Settings {
    business_name: string;
    clothing_brand_name: string;
    confectionery_brand_name: string;
    tagline: string;
    hero_headline: string;
    hero_subheadline: string;
    whatsapp_number: string;
    currency_code: string;
    currency_symbol: string;
    contact_email: string;
    contact_phone: string;
    address: string;
    instagram_url: string;
    facebook_url: string;
    delivery_info: string;
    logo_url: string;
    logo_dark_url: string;
}

export interface OrderEnquiry {
    id: number;
    reference: string;
    status: 'pending' | 'contacted' | 'completed' | 'cancelled';
    customer_name?: string | null;
    customer_phone?: string | null;
    customer_address?: string | null;
    customer_notes?: string | null;
    items: CartItem[];
    subtotal: number;
    delivery_fee: number;
    total: number;
    whatsapp_message: string;
    whatsapp_url: string;
    created_at: string;
}

export type PageProps<T = Record<string, unknown>> = T & {
    auth: {
        user: User | null;
    };
    settings: Settings;
    cartCount: number;
    cartSubtotal: string;
    navCategories: Array<{
        id: number;
        name: string;
        slug: string;
        brand_name: string;
        icon?: string;
    }>;
    flash: {
        success?: string | null;
        error?: string | null;
        warning?: string | null;
        info?: string | null;
    };
};
