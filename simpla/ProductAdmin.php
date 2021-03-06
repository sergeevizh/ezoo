<?php

/**
 * Simpla CMS
 *
 * @copyright    2016 Denis Pikusov
 * @link        http://simplacms.ru
 * @author        Denis Pikusov
 *
 */

require_once('api/Simpla.php');

class ProductAdmin extends Simpla
{
    public function fetch()
    {
        $options = array();
        $product_categories = array();
        $variants = array();
        $images = array();
        $related_products = array();
        $deliveries_products = array();

        if ($this->request->method('post') && !empty($_POST)) {
            $product = new stdClass;
            $product->id = $this->request->post('id', 'integer');
            $product->name = $this->request->post('name');
            $product->name_yan_market = $this->request->post('name_yan_market');
            $product->name_onliner_market = $this->request->post('name_onliner_market');
            $product->visible = $this->request->post('visible', 'boolean');
            $product->featured = $this->request->post('featured');
            $product->brand_id = $this->request->post('brand_id', 'integer');

            $product->pickup = $this->request->post('pickup');
            $product->lecense = $this->request->post('lecense');
            $product->marketing_offer = $this->request->post('marketing_offer');

            /* featured_cart */
            $product->featured_cart = $this->request->post('featured_cart');
            /* END featured_cart */

            $product->url = trim($this->request->post('url', 'string'));
            $product->meta_title = $this->request->post('meta_title');
            $product->meta_keywords = $this->request->post('meta_keywords');
            $product->meta_description = $this->request->post('meta_description');

            $product->annotation = $this->request->post('annotation');
            $product->body = $this->request->post('body');

            $product->sale_double_item = $this->request->post('sale_double_item', 'boolean');
            $product->sale_double_item_value = $this->request->post('sale_double_item_value', 'integer');
            $product->sale_double_item_sam = $this->request->post('sale_double_item_sam', 'boolean');
            $product->sale_double_item_sam_value = $this->request->post('sale_double_item_sam_value', 'integer');

            // ???????????????? ????????????
            if ($this->request->post('variants')) {
                foreach ($this->request->post('variants') as $n => $va) {
                    foreach ($va as $i => $v) {
                        if (empty($variants[$i])) {
                            $variants[$i] = new stdClass;
                        }
                        $variants[$i]->$n = $v;
                    }
                }
            }

            // ?????????????????? ????????????
            $product_categories = $this->request->post('categories');
            if (is_array($product_categories)) {
                $pc = array();
                foreach ($product_categories as $c) {
                    $x = new stdClass;
                    $x->id = $c;
                    $pc[] = $x;
                }
                $product_categories = $pc;
            }

            // ???????????????? ????????????
            $options = $this->request->post('options');
            if (is_array($options)) {
                $po = array();
                foreach ($options as $f_id => $val) {
                    $po[$f_id] = new stdClass;
                    $po[$f_id]->feature_id = $f_id;
                    $po[$f_id]->value = $val;
                }
                $options = $po;
            }

            // ?????????????????? ????????????
            if (is_array($this->request->post('related_products'))) {
                $rp = array();
                foreach ($this->request->post('related_products') as $p) {
                    $rp[$p] = new stdClass;
                    $rp[$p]->product_id = $product->id;
                    $rp[$p]->related_id = $p;
                }
                $related_products = $rp;
            }

            // ???? ?????????????????? ???????????? ???????????????? ????????????.
            if (empty($product->name)) {
                $this->design->assign('message_error', 'empty_name');
                if (!empty($product->id)) {
                    $images = $this->products->get_images(array('product_id' => $product->id));
                }
            } // ???? ?????????????????? ???????????????????? URL ????????????????.
            elseif (($p = $this->products->get_product($product->url)) && $p->id != $product->id) {
                $this->design->assign('message_error', 'url_exists');
                if (!empty($product->id)) {
                    $images = $this->products->get_images(array('product_id' => $product->id));
                }
            } else {
                if (empty($product->id)) {
                    if (is_array($product_categories)){
                        foreach ($product_categories as $p_c){

                            if ($p_c->id == '766835' || $p_c->id == '766888' || $p_c->parent_id == '766835' || $p_c->parent_id == '766888'){
                                $product->pickup = 1;
                                break;
                            }
                        }
                    }
                    $product->id = $this->products->add_product($product);
                    $product = $this->products->get_product($product->id);
                    $this->design->assign('message_success', 'added');
                } else {
                    $this->products->update_product($product->id, $product);
                    $product = $this->products->get_product($product->id);
                    $this->design->assign('message_success', 'updated');
                }

                if ($product->id) {
                    // ?????????????????? ????????????
                    $query = $this->db->placehold('DELETE FROM __products_categories WHERE product_id=?', $product->id);
                    $this->db->query($query);
                    if (is_array($product_categories)) {
                        foreach ($product_categories as $i => $category) {
                            $this->categories->add_product_category($product->id, $category->id, $i);
                        }
                    }

                    // ????????????????
                    if (is_array($variants)) {
                        $variants_ids = array();
                        foreach ($variants as $index => &$variant) {
                            if ($variant->stock == '???' || $variant->stock == '') {
                                $variant->stock = null;
                            }

                            // ?????????????? ????????
                            if (!empty($_POST['delete_attachment'][$index])) {
                                $this->variants->delete_attachment($variant->id);
                            }

                            // ?????????????????? ??????????
                            if (!empty($_FILES['attachment']['tmp_name'][$index]) && !empty($_FILES['attachment']['name'][$index])) {
                                $attachment_tmp_name = $_FILES['attachment']['tmp_name'][$index];
                                $attachment_name = $_FILES['attachment']['name'][$index];
                                move_uploaded_file($attachment_tmp_name, $this->config->root_dir . '/' . $this->config->downloads_dir . $attachment_name);
                                $variant->attachment = $attachment_name;
                            }

                            if (!empty($variant->id)) {
                                $this->variants->update_variant($variant->id, $variant);
                            } else {
                                $variant->product_id = $product->id;
                                $variant->id = $this->variants->add_variant($variant);
                            }
                            $variant = $this->variants->get_variant($variant->id);
                            if (!empty($variant->id)) {
                                $variants_ids[] = $variant->id;
                            }
                        }


                        // ?????????????? ???????????????????????? ????????????????
                        $current_variants = $this->variants->get_variants(array('product_id' => $product->id));
                        foreach ($current_variants as $current_variant) {
                            if (!in_array($current_variant->id, $variants_ids)) {
                                $this->variants->delete_variant($current_variant->id);
                            }
                        }

                        //if(!empty($))

                        // ??????????????????????????  ????????????????
                        asort($variants_ids);
                        $i = 0;
                        foreach ($variants_ids as $variant_id) {
                            $this->variants->update_variant($variants_ids[$i], array('position' => $variant_id));
                            $i++;
                        }
                    }

                    // ???????????????? ??????????????????????
                    $images = (array)$this->request->post('images');
                    $current_images = $this->products->get_images(array('product_id' => $product->id));
                    foreach ($current_images as $image) {
                        if (!in_array($image->id, $images)) {
                            $this->products->delete_image($image->id);
                        }
                    }

                    // ?????????????? ??????????????????????
                    if ($images = $this->request->post('images')) {
                        $i = 0;
                        foreach ($images as $id) {
                            $this->products->update_image($id, array('position' => $i));
                            $i++;
                        }
                    }
                    // ???????????????? ??????????????????????
                    if ($images = $this->request->files('images')) {
                        for ($i = 0; $i < count($images['name']); $i++) {
                            if ($image_name = $this->image->upload_image($images['tmp_name'][$i], $images['name'][$i])) {
                                $this->products->add_image($product->id, $image_name);
                            } else {
                                $this->design->assign('error', 'error uploading image');
                            }
                        }
                    }
                    // ???????????????? ?????????????????????? ???? ?????????????????? ?? drag-n-drop ????????????
                    if ($images = $this->request->post('images_urls')) {
                        foreach ($images as $url) {
                            // ???????? ???? ???????????? ?????????? ?? ???????? ???? ??????????????????
                            if (!empty($url) && $url != 'http://' && strstr($url, '/') !== false) {
                                $this->products->add_image($product->id, $url);
                            } elseif ($dropped_images = $this->request->files('dropped_images')) {
                                $key = array_search($url, $dropped_images['name']);
                                if ($key !== false && $image_name = $this->image->upload_image($dropped_images['tmp_name'][$key], $dropped_images['name'][$key])) {
                                    $this->products->add_image($product->id, $image_name);
                                }
                            }
                        }
                    }
                    $images = $this->products->get_images(array('product_id' => $product->id));

                    // ???????????????????????????? ????????????

                    // ???????????? ?????? ???? ????????????
                    foreach ($this->features->get_product_options($product->id) as $po) {
                        $this->features->delete_option($product->id, $po->feature_id);
                    }

                    // ???????????????? ?????????????? ??????????????????
                    $category_features = array();
                    foreach ($this->features->get_features(array('category_id' => $product_categories[0])) as $f) {
                        $category_features[] = $f->id;
                    }

                    if (is_array($options)) {
                        foreach ($options as $option) {
                            if (in_array($option->feature_id, $category_features)) {
                                $this->features->update_option($product->id, $option->feature_id, $option->value);
                            }
                        }
                    }

                    // ?????????? ????????????????????????????
                    $new_features_names = $this->request->post('new_features_names');
                    $new_features_values = $this->request->post('new_features_values');
                    if (is_array($new_features_names) && is_array($new_features_values)) {
                        foreach ($new_features_names as $i => $name) {
                            $value = trim($new_features_values[$i]);
                            if (!empty($name) && !empty($value)) {
                                $query = $this->db->placehold("SELECT * FROM __features WHERE name=? LIMIT 1", trim($name));
                                $this->db->query($query);
                                $feature_id = $this->db->result('id');
                                if (empty($feature_id)) {
                                    $feature_id = $this->features->add_feature(array('name' => trim($name)));
                                }
                                $this->features->add_feature_category($feature_id, reset($product_categories)->id);
                                $this->features->update_option($product->id, $feature_id, $value);
                            }
                        }
                        // ???????????????? ????????????
                        $options = $this->features->get_product_options($product->id);
                    }

                    // ?????????????????? ????????????
                    $query = $this->db->placehold('DELETE FROM __related_products WHERE product_id=?', $product->id);
                    $this->db->query($query);
                    if (is_array($related_products)) {
                        $pos = 0;
                        foreach ($related_products as $i => $related_product) {
                            $this->products->add_related_product($product->id, $related_product->related_id, $pos++);
                        }
                    }
                }
            }
            if ($this->request->post('deliveries_products')) {
                foreach ($this->request->post('deliveries_products') as $n => $va) {
                    foreach ($va as $i => $v) {
                        if (empty($deliveries_products[$i])) {
                            $deliveries_products[$i] = new stdClass;
                        }
                        $deliveries_products[$i]->$n = $v;
                    }
                }
            }
            if (!empty($product->id)) {
                $this->db->query('DELETE FROM __delivery_products WHERE product_id=?', $product->id);
                foreach ($deliveries_products as $delivery_product) {
                    $delivery_product->product_id = $product->id;
                    $this->db->query("INSERT INTO __delivery_products SET ?%", $delivery_product);
                }
            }
            //header('Location: '.$this->request->url(array('message_success'=>'updated')));
        } else {
            $id = $this->request->get('id', 'integer');
            $product = $this->products->get_product(intval($id));

            if ($product) {

                // ?????????????????? ????????????
                $product_categories = $this->categories->get_categories(array('product_id' => $product->id));

                // ???????????????? ????????????
                $variants = $this->variants->get_variants(array('product_id' => $product->id));

                // ?????????????????????? ????????????
                $images = $this->products->get_images(array('product_id' => $product->id));

                // ???????????????? ????????????
                $options = $this->features->get_options(array('product_id' => $product->id));

                // ?????????????????? ????????????
                $related_products = $this->products->get_related_products(array('product_id' => $product->id));

                $this->db->query("SELECT * FROM __delivery_products WHERE product_id=?", $product->id);
                $deliveries_products = $this->db->results();
            } else {
                // ?????????? ??????????????
                $product = new stdClass;
                $product->visible = 1;
            }
        }


        if (empty($variants)) {
            $variants = array(1);
        }

        if (empty($product_categories)) {
            if ($category_id = $this->request->get('category_id')) {
                $product_categories[0]->id = $category_id;
            } else {
                $product_categories = array(1);
            }
        }
        if (empty($product->brand_id) && $brand_id = $this->request->get('brand_id')) {
            $product->brand_id = $brand_id;
        }

        if (!empty($related_products)) {
            $r_products = array();
            foreach ($related_products as &$r_p) {
                $r_products[$r_p->related_id] = &$r_p;
            }
            $temp_products = $this->products->get_products(array('id' => array_keys($r_products), 'limit' => count(array_keys($r_products))));
            foreach ($temp_products as $temp_product) {
                $r_products[$temp_product->id] = $temp_product;
            }

            $related_products_images = $this->products->get_images(array('product_id' => array_keys($r_products)));
            foreach ($related_products_images as $image) {
                $r_products[$image->product_id]->images[] = $image;
            }
        }

        if (is_array($options)) {
            $temp_options = array();
            foreach ($options as $option) {
                $temp_options[$option->feature_id] = $option;
            }
            $options = $temp_options;
        }


        $this->design->assign('product', $product);

        $this->design->assign('product_categories', $product_categories);
        $this->design->assign('product_variants', $variants);
        $this->design->assign('product_images', $images);
        $this->design->assign('options', $options);
        $this->design->assign('related_products', $related_products);

        // ?????? ????????????
        $brands = $this->brands->get_brands();
        $this->design->assign('brands', $brands);

        // ?????? ??????????????????
        $categories = $this->categories->get_categories_tree();
        $this->design->assign('categories', $categories);

        // ?????? ???????????????? ????????????
        $category = reset($product_categories);
        if (!is_object($category)) {
            $category = reset($categories);
        }
        if (is_object($category)) {
            $features = $this->features->get_features(array('category_id' => $category->id));
            $this->design->assign('features', $features);
        }

        if (empty($deliveries_products)) {
            $deliveries_products = array();
        }
        $this->design->assign('deliveries_products', $deliveries_products);

        $deliveries = $this->delivery->get_deliveries();
        $this->design->assign('deliveries', $deliveries);

        $regions = $this->regions->get_regions();
        $this->design->assign('regions', $regions);

        return $this->design->fetch('product.tpl');
    }
}
