<?php
require_once 'BaseModel.php';

class Product extends BaseModel {
    protected $table = 'products';
    
    // Get products with vendor and category details
    public function getProductsWithDetails($conditions = [], $limit = null, $offset = 0) {
        $sql = "SELECT p.*, v.shop_name, c.name as category_name,
                       (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image,
                       (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND status = 'approved') as avg_rating,
                       (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND status = 'approved') as review_count
                FROM products p
                LEFT JOIN vendors v ON p.vendor_id = v.id
                LEFT JOIN categories c ON p.category_id = c.id";
        
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                if ($field === 'search') {
                    $whereClause[] = "(p.name LIKE ? OR p.description LIKE ?)";
                    $params[] = "%$value%";
                    $params[] = "%$value%";
                } elseif ($field === 'price_min') {
                    $whereClause[] = "p.price >= ?";
                    $params[] = $value;
                } elseif ($field === 'price_max') {
                    $whereClause[] = "p.price <= ?";
                    $params[] = $value;
                } else {
                    $whereClause[] = "p.$field = ?";
                    $params[] = $value;
                }
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        return $this->query($sql, $params);
    }
    
    // Get single product with all details
    public function getProductDetails($id) {
        $sql = "SELECT p.*, v.shop_name, v.id as vendor_id, c.name as category_name,
                       (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND status = 'approved') as avg_rating,
                       (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND status = 'approved') as review_count
                FROM products p
                LEFT JOIN vendors v ON p.vendor_id = v.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ?";
        
        $product = $this->queryOne($sql, [$id]);
        
        if ($product) {
            // Get product images
            $product['images'] = $this->getProductImages($id);
            
            // Get product attributes
            $product['attributes'] = $this->getProductAttributes($id);
        }
        
        return $product;
    }
    
    // Get product images
    public function getProductImages($productId) {
        $sql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC";
        return $this->query($sql, [$productId]);
    }
    
    // Get product attributes
    public function getProductAttributes($productId) {
        $sql = "SELECT * FROM product_attributes WHERE product_id = ? ORDER BY attribute_name, attribute_value";
        return $this->query($sql, [$productId]);
    }
    
    // Create product with images
    public function createProduct($productData, $images = [], $attributes = []) {
        try {
            $this->pdo->beginTransaction();
            
            // Generate slug
            $productData['slug'] = $this->generateUniqueSlug($productData['name']);
            $productData['created_at'] = date('Y-m-d H:i:s');
            
            // Create product
            $productId = $this->create($productData);
            
            if (!$productId) {
                throw new Exception('Failed to create product');
            }
            
            // Add images
            if (!empty($images)) {
                $this->addProductImages($productId, $images);
            }
            
            // Add attributes
            if (!empty($attributes)) {
                $this->addProductAttributes($productId, $attributes);
            }
            
            $this->pdo->commit();
            return $productId;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    
    // Add product images
    public function addProductImages($productId, $images) {
        $sql = "INSERT INTO product_images (product_id, image_url, alt_text, is_primary, sort_order) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($images as $index => $image) {
            $stmt->execute([
                $productId,
                $image['url'],
                $image['alt'] ?? '',
                $index === 0 ? 1 : 0, // First image is primary
                $index
            ]);
        }
    }
    
    // Add product attributes
    public function addProductAttributes($productId, $attributes) {
        $sql = "INSERT INTO product_attributes (product_id, attribute_name, attribute_value, price_adjustment, stock_quantity) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($attributes as $attribute) {
            $stmt->execute([
                $productId,
                $attribute['name'],
                $attribute['value'],
                $attribute['price_adjustment'] ?? 0,
                $attribute['stock_quantity'] ?? 0
            ]);
        }
    }
    
    // Generate unique slug
    private function generateUniqueSlug($name, $id = null) {
        $slug = generateSlug($name);
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $sql = "SELECT id FROM products WHERE slug = ?";
            $params = [$slug];
            
            if ($id) {
                $sql .= " AND id != ?";
                $params[] = $id;
            }
            
            $existing = $this->queryOne($sql, $params);
            
            if (!$existing) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    // Get featured products
    public function getFeaturedProducts($limit = 8) {
        return $this->getProductsWithDetails(['featured' => 1, 'status' => 'active'], $limit);
    }
    
    // Get products by category
    public function getProductsByCategory($categoryId, $limit = null, $offset = 0) {
        return $this->getProductsWithDetails(['category_id' => $categoryId, 'status' => 'active'], $limit, $offset);
    }
    
    // Get products by vendor
    public function getProductsByVendor($vendorId, $limit = null, $offset = 0) {
        return $this->getProductsWithDetails(['vendor_id' => $vendorId, 'status' => 'active'], $limit, $offset);
    }
    
    // Search products
    public function searchProducts($query, $filters = [], $limit = null, $offset = 0) {
        $conditions = ['search' => $query, 'status' => 'active'];
        
        // Add filters
        if (isset($filters['category_id'])) {
            $conditions['category_id'] = $filters['category_id'];
        }
        
        if (isset($filters['price_min'])) {
            $conditions['price_min'] = $filters['price_min'];
        }
        
        if (isset($filters['price_max'])) {
            $conditions['price_max'] = $filters['price_max'];
        }
        
        return $this->getProductsWithDetails($conditions, $limit, $offset);
    }
    
    // Update stock quantity
    public function updateStock($productId, $quantity) {
        return $this->update($productId, ['stock_quantity' => $quantity]);
    }
    
    // Decrease stock
    public function decreaseStock($productId, $quantity) {
        $sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$quantity, $productId, $quantity]);
    }
}
?>
