<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Api\V1\Rest\Service\NotificationsService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Http\Request;
use Zend\Router\Http\TreeRouteStack;
use Zend\Router\RouteStackInterface;
use Api\V1\Rest\Model\CompetitionsMapper;
use Api\V1\Rest\Model\CompaniesMapper;
use Api\V1\Rest\Model\UsersMapper;
use Api\V1\Rest\Model\ProductsMapper;
use Api\V1\Rest\Model\CompanyMembersMapper;
use Api\V1\Rest\Model\ReviewsMapper;
use Api\V1\Rest\Model\CompetitionTagsMapper;
use Api\V1\Rest\Model\TagsMapper;
use Api\V1\Rest\Service\ReviewsService;

class StructuredDataHelper extends AbstractHelper {

    protected $config;
    protected $db;
    protected $router;
    protected $request;
    protected $reviews_service;

    public function __construct(
    $config, AdapterInterface $db, TreeRouteStack $router, Request $request, ReviewsService $reviews_service) {
        $this->config = $config;
        $this->db = $db;
        $this->router = $router;
        $this->request = $request;
        $this->reviews_service = $reviews_service;
    }

    public function renderStructuredData() {

        $uri_path = $this->request->getUri()->getPath();
        $uri_path = trim($uri_path);
        $uri_path = preg_match('/\/(?<path>.*)/', $uri_path, $match);
        $uri_path = $match['path'];
        $structured_data='';
        if (preg_match('/^(product|products)\/(?<product_id>[0-9]+)\/[a-z0-9-A-Z_-]+(\/)?$/', $uri_path, $match) == 1) {

            $product = ProductsMapper::findOne([], ['id' => $match['product_id']]);
            $rating_score = $this->reviews_service->getProductReviewsScore($product['id']);
            $company = UsersMapper::findOne(['name'], ['id' => $product['user_id']]);
            $company['name'] = ucwords($company['name']);
            $product['title'] = ucwords($product['title']);

            $structured_data = <<<EOD
<div  itemscope itemtype="http://schema.org/Product">
  <span itemprop="brand">{$company['name']}</span>
  <span itemprop="name">{$product['title']}</span>
  <img itemprop="image" src="anvil_executive.jpg" alt="Executive Anvil logo" />
  <span itemprop="description">{$product['description']}</span>
  
  <span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
    <span itemprop="ratingValue">{$rating_score['overall_rating']}</span> stars, based on <span itemprop="reviewCount">{$rating_score['total_reviews']}
      </span> reviews
  </span>
</div>
EOD;
        }

        $this->generateHtmlData($structured_data);
    }

    public function render() {

        CompetitionTagsMapper::setDbAdapter($this->db);
        CompetitionsMapper::setDbAdapter($this->db);
        TagsMapper::setDbAdapter($this->db);
        ReviewsMapper::setDbAdapter($this->db);
        UsersMapper::setDbAdapter($this->db);
        ProductsMapper::setDbAdapter($this->db);
        CompanyMembersMapper::setDbAdapter($this->db);

        $uri_path = $this->request->getUri()->getPath();
        $uri_path = trim($uri_path);
        $uri_path = preg_match('/\/(?<path>.*)/', $uri_path, $match);
        $uri_path = $match['path'];

        $page_data = [];

        if (empty($uri_path)) {
            //HOME
            $page_data = [
                'title' => 'For the latest in fitness trends, services and products visit Write Up',
                'meta_tags' => [
                    [
                        'name' => 'keywords',
                        'content' => 'personal trainer, fitness, fitness reviews'
                    ],
                    [
                        'name' => 'description',
                        'content' => 'Write Up is an online review platform for the fitness industry. this allows businesses to receive reviews on products or services, and allows customers to read honest feedback before making a purchase.'
                    ],
                    [
                        'name' => 'og:title',
                        'content' => 'For the latest in fitness trends, services and products visit Write Up'
                    ],
                    [
                        'name' => 'og:type',
                        'content' => 'website'
                    ],
                    [
                        'name' => 'og:url',
                        'content' => $this->config['site_url']
                    ],
                    [
                        'name' => 'og:site_name',
                        'content' => $this->config['site_details']['site_title']
                    ],
                    [
                        'name' => 'og:description',
                        'content' => 'Write Up is an online review platform for the fitness industry. this allows businesses to receive reviews on products or services, and allows customers to read honest feedback before making a purchase.'
                    ],
                    [
                        'name' => 'og:image',
                        'content' => $this->config['site_url'] . '/assets/images/writeup-logo.png'
                    ],
                    [
                        'name' => 'twitter:card',
                        'content' => 'summary_large_image'
                    ],
                    [
                        'name' => 'twitter:site',
                        'content' => '@WriteUpReviews'
                    ],
                    [
                        'name' => 'twitter:title',
                        'content' => 'For the latest in fitness trends, services and products visit Write Up'
                    ],
                    [
                        'name' => 'twitter:description',
                        'content' => 'Write Up is an online review platform for the fitness industry. this allows businesses to receive reviews on products or services, and allows customers to read honest feedback before making a purchase.'
                    ],
                    [
                        'name' => 'twitter:image',
                        'content' => $this->config['site_url'] . '/assets/images/writeup-logo.png'
                    ],
                ]
            ];
        } else {
            if (preg_match('/^(terms|terms-and-conditions|terms-conditions)$/', $uri_path) == 1) {
                $page_data = [
                    'title' => 'Terms & Conditions | Write Up Fitness Reviews',
                ];
            } else {
                if (preg_match('/^(about|aboutus|about-us)$/', $uri_path) == 1) {
                    $page_data = [
                        'title' => 'About Us | Write Up Fitness Reviews',
                        'meta_tags' => [
                            [
                                'name' => 'description',
                                'content' => 'Write Up is an online review platform for anything regarding fitness. You can read honest feedback and product reviews before making a final decision.'
                            ],
                        ]
                    ];
                } else {
                    if (preg_match('/^(contact|contactus|contact-us)$/', $uri_path) == 1) {
                        $page_data = [
                            'title' => 'Contact | Write Up Fitness Reviews',
                            'meta_tags' => [
                                [
                                    'name' => 'description',
                                    'content' => "If you have any questions, feedback, or issues on any of Write Up services. Get in touch by filling the contact form. We'd love to hear from you"
                                ],
                            ]
                        ];
                    } else {
                        if (preg_match('/^(faq|faqs)$/', $uri_path) == 1) {
                            $page_data = [
                                'title' => 'FAQ (Frequently Asked Questions) | Write Up Fitness Reviews',
                                'meta_tags' => [
                                    [
                                        'name' => 'description',
                                        'content' => "If you have any questions, concerns or queries please read Write Up Frequently Asked Questions."
                                    ],
                                ]
                            ];
                        } else {
                            //COMPETITIONS
                            if (preg_match('/^(competitions|all-competitions)$/', $uri_path) == 1) {
                                $page_data = [
                                    'title' => 'Participate in Fitness Industry Competitions | Write Up Fitness Reviews',
                                    'meta_tags' => [
                                        [
                                            'name' => 'keywords',
                                            'content' => "Fitness competitions, competitions"
                                        ],
                                        [
                                            'name' => 'description',
                                            'content' => "Excellent chance to get involved in health and fitness competitions to win exciting prizes by top fitness companies and individuals. Increase your chances of winning by regular participation. Enter Competitions Now!"
                                        ],
                                    ]
                                ];
                            } else {
                                //COMPETITION
                                if (preg_match('/^competition(s)?\/(?<competition_id>[0-9]+)\/[a-z0-9-A-Z_-]+$/', $uri_path, $match) == 1) {
                                    $competition = CompetitionsMapper::findOne(['name'], ['id' => $match['competition_id']]);
                                    $competition_tags = CompetitionTagsMapper::findAll(['tag_id'], ['competition_id' => $match['competition_id']]);
                                    $competition['name'] = ucwords($competition['name']);
                                    if (!empty($competition_tags)) {
                                        $tags = [];
                                        foreach ($competition_tags as $k => $v) {
                                            $tag = TagsMapper::findOne(['title'], ['id' => $v['tag_id']]);
                                            $tags[] = ucwords($tag['title']);
                                        }
                                        $page_data['meta_tags'][] = [
                                            'name' => 'keywords',
                                            'content' => implode(', ', $tags)
                                        ];
                                    }
                                    $page_data['title'] = "Participate in " . ($competition['name']) . " Competition and Win Prizes | Write Up Fitness Reviews";
                                    $page_data['meta_tags'][] = [
                                        'name' => 'description',
                                        'content' => "Participate in " . ($competition['name']) . " competition to win exciting prizes. Never miss the health and fitness competitions again. Participate Now!!"
                                    ];
                                } else {
                                    //COMPANY
                                    if (preg_match('/^(company|companies)\/(?<company_id>[0-9]+)\/[a-z0-9-A-Z_-]+$/', $uri_path, $match) == 1) {

                                        $company = UsersMapper::findOne([], ['id' => $match['company_id']]);

                                        $rating_score = $this->reviews_service->getCompanyReviewsScore($match['company_id']);

                                        $company['name'] = ucwords($company['name']);
                                        $page_data['title'] = ($company['name']) . " Reviews | Write Up Fitness Review";
                                        $page_data['meta_tags'] = [
                                            [
                                                'name' => 'description',
                                                'content' => "Read all of " . ($company['name']) . " company reviews posted by genuine service users. If you have used " . ($company['name']) . " company services please write a review to help decide the right product for them."
                                            ],
                                            [
                                                'name' => 'itemReviewed',
                                                'content' => $company['name']
                                            ],
                                            [
                                                'name' => 'reviewCount',
                                                'content' => $rating_score['total_reviews']
                                            ],
                                            [
                                                'name' => 'ratingValue',
                                                'content' => $rating_score['overall_rating']
                                            ],
                                            [
                                                'name' => 'bestRating',
                                                'content' => 5
                                            ],
                                        ];
                                    } else {
                                        //USER
                                        if (preg_match('/^(user|users)\/(?<user_id>[0-9]+)\/[0-9A-Za-z_-]+$/', $uri_path, $match) == 1) {
                                            $user = UsersMapper::findOne(['name'], ['id' => $match['user_id']]);
                                            $user['name'] = ucwords($user['name']);
                                            $rating_score = $this->reviews_service->getUserReviewsScore($match['user_id']);
                                            $page_data['title'] = ($user['name']) . " Reviews | Write Up Fitness Review";
                                            $page_data['meta_tags'] = [
                                                [
                                                    'name' => 'description',
                                                    'content' => "Read all of " . ($user['name']) . " user reviews posted by genuine service users. If you have used " . ($user['name']) . " user services please write a review to help decide the right product for them."
                                                ],
                                                [
                                                    'name' => 'itemReviewed',
                                                    'content' => $user['name']
                                                ],
                                                [
                                                    'name' => 'reviewCount',
                                                    'content' => $rating_score['total_reviews']
                                                ],
                                                [
                                                    'name' => 'ratingValue',
                                                    'content' => $rating_score['overall_rating']
                                                ],
                                                [
                                                    'name' => 'bestRating',
                                                    'content' => 5
                                                ],
                                            ];
                                        } else {
                                            //COMPANY REVIEW
                                            if (preg_match('/^(company|companies)\/(?<company_id>[0-9]+)\/[a-z0-9-A-Z_-]+\/(review|reviews)\/(?<review_id>[0-9]+)(\/.+)?$/', $uri_path, $match) == 1) {
                                                $company = UsersMapper::findOne(['name'], ['id' => $match['company_id']]);
                                                $review = ReviewsMapper::findOne(['title', 'from_user'], ['id' => $match['review_id']]);
                                                $review_from_user = UsersMapper::findOne(['name'], ['id' => $review['from_user']]);
                                                $review_from_user ['name'] = ucwords($review_from_user ['name']);
                                                $company['name'] = ucwords($company['name']);
                                                $review['title'] = ucwords($review['title']);
                                                $page_data['title'] = ($review['title']) . " - " . ($company['name']) . " Company Reviews | Write Up Fitness Reviews";
                                                $page_data['meta_tags'][] = [
                                                    'name' => 'description',
                                                    'content' => "Read and reply to {$review_from_user ['name']} user review for {$company['name']} fitness company. Help others find and discover the product/services that are just right for them."
                                                ];
                                            } else {
                                                //USER REVIEW
                                                if (preg_match('/^(user|users)\/(?<user_id>[0-9]+)\/[a-z0-9-A-Z_-]+\/(review|reviews)\/(?<review_id>[0-9]+)(\/.+)?$/', $uri_path, $match) == 1) {
                                                    $user = UsersMapper::findOne(['name'], ['id' => $match['user_id']]);
                                                    $company = CompanyMembersMapper::findOne(['company_id'], ['user_id' => $match['user_id'], 'verified' => 1]);
                                                    $company = UsersMapper::findOne(['name'], ['id' => $company['company_id']]);
                                                    $company['name'] = ucwords($company['name']);
                                                    $review = ReviewsMapper::findOne(['title', 'from_user'], ['id' => $match['review_id']]);
                                                    $review_from_user = UsersMapper::findOne(['name'], ['id' => $review['from_user']]);
                                                    $page_data['title'] = ucwords($review['title']) . " - " . ucwords($user['name']) . " Detailed Review By " . ucwords($review_from_user['name']) . " | Write Up Fitness Reviews";
                                                    $page_data['meta_tags'][] = [
                                                        'name' => 'description',
                                                        'content' => "Read and reply to {$review_from_user ['name']} user review for {$user['name']} professional of {$company['name']} fitness company. Help others find and discover the best professional services that are just right for them."
                                                    ];
                                                } else {
                                                    if (preg_match('/^(search)$/', $uri_path) == 1) {
                                                        $page_data = [
                                                            'title' => 'Search company, employee and product reviews | Write Up Fitness Review',
                                                            'meta_tags' => [
                                                                [
                                                                    'name' => 'description',
                                                                    'content' => "Find relevant information about each company, employee or product reviews. "
                                                                ],
                                                            ]
                                                        ];
                                                    } else {
                                                        //USER REVIEWS
                                                        if (preg_match('/^(user|users)\/(?<user_id>[0-9]+)\/[a-z0-9-A-Z_-]+\/reviews$/', $uri_path, $match) == 1) {
                                                            $user = UsersMapper::findOne(['name'], ['id' => $match['user_id']]);
                                                            $page_data = [
                                                                'title' => ucwords($user['name']) . ' Professional Services Reviews | Write Up Fitness Review',
                                                                'meta_tags' => [
                                                                    [
                                                                        'name' => 'description',
                                                                        'content' => "Read all the reviews and ratings posted for " . ucwords($user['name']) . " professional services. Now is your chance to have your voice heard!"
                                                                    ],
                                                                ]
                                                            ];
                                                        } else {
                                                            //COMPANY COMPETITIONS
                                                            if (preg_match('/^(company|companies)\/(?<company_id>[0-9]+)\/[a-z0-9-A-Z_-]+\/competitions(\/)?$/', $uri_path, $match) == 1) {
                                                                $company = UsersMapper::findOne(['name'], ['id' => $match['company_id']]);
                                                                $company['name'] = ucwords($company['name']);
                                                                $page_data = [
                                                                    'title' => "List of All Competitions Organised by {$company['name']} | Write Up Fitness Review",
                                                                    'meta_tags' => [
                                                                        [
                                                                            'name' => 'description',
                                                                            'content' => "Checkout all the live, upcoming and past competitions organised by {$company['name']}. Join the live competitions to win exciting prizes from your favourite company."
                                                                        ],
                                                                    ]
                                                                ];
                                                            } else {
                                                                //PRODUCT
                                                                if (preg_match('/^(product|products)\/(?<product_id>[0-9]+)\/[a-z0-9-A-Z_-]+(\/)?$/', $uri_path, $match) == 1) {

                                                                    $product = ProductsMapper::findOne([], ['id' => $match['product_id']]);
                                                                    $rating_score = $this->reviews_service->getProductReviewsScore($product['id']);
                                                                    $company = UsersMapper::findOne(['name'], ['id' => $product['user_id']]);
                                                                    $company['name'] = ucwords($company['name']);
                                                                    $product['title'] = ucwords($product['title']);
                                                                    $page_data = [
                                                                        'title' => " Read {$product['title']} Reviews Online | WriteUp Fitness Review",
                                                                        'meta_tags' => [
                                                                            [
                                                                                'name' => 'description',
                                                                                'content' => "Read {$product['title']} product reviews made by {$company['name']} company. Check all genuine reviews and ratings written for {$product['title']} from the WriteUp website now!"
                                                                            ],
                                                                            [
                                                                                'name' => 'itemReviewed',
                                                                                'content' => $product['title']
                                                                            ],
                                                                            [
                                                                                'name' => 'reviewCount',
                                                                                'content' => $rating_score['total_reviews']
                                                                            ],
                                                                            [
                                                                                'name' => 'ratingValue',
                                                                                'content' => $rating_score['overall_rating']
                                                                            ],
                                                                            [
                                                                                'name' => 'bestRating',
                                                                                'content' => 5
                                                                            ],
                                                                        ]
                                                                    ];
                                                                } else {
                                                                    if (preg_match('/^(privacy|privacy-and-policy)$/', $uri_path) == 1) {
                                                                        $page_data = [
                                                                            'title' => 'Privacy Policy | Write Up Fitness Reviews ',
                                                                        ];
                                                                    } else {
                                                                        if (preg_match('/^(login)$/', $uri_path) == 1) {
                                                                            $page_data = [
                                                                                'title' => 'Login | Write Up Fitness Reviews',
                                                                            ];
                                                                        } else {
                                                                            if (preg_match('/^(register|signup|user-signup|user-register|registration|user-registration)$/', $uri_path) == 1) {
                                                                                $page_data = [
                                                                                    'title' => 'User Signup | WriteUp Fitness Reviews',
                                                                                ];
                                                                            } else {
                                                                                if (preg_match('/^(forgot-password)$/', $uri_path) == 1) {
                                                                                    $page_data = [
                                                                                        'title' => 'Forgotten Password | Write Up Fitness Reviews',
                                                                                    ];
                                                                                } else {
                                                                                    //COMPANY REVIEWS
                                                                                    if (preg_match('/^(company|companies)\/(?<company_id>[0-9]+)\/[a-z0-9-A-Z_-]+\/reviews$/', $uri_path, $match) == 1) {
                                                                                        $user = UsersMapper::findOne(['name'], ['id' => $match['company_id']]);
                                                                                        $user['name'] = ucwords($user['name']);
                                                                                        $page_data = [
                                                                                            'title' => ($user['name']) . ' Reviews | Write Up Fitness Review',
                                                                                            'meta_tags' => [
                                                                                                [
                                                                                                    'name' => 'description',
                                                                                                    'content' => "Read all the reviews and ratings of " . ($user['name']) . "."
                                                                                                ],
                                                                                            ]
                                                                                        ];
                                                                                    } else {
                                                                                        //PRODUCT REVIEWS
                                                                                        if (preg_match('/^(product|products)\/(?<product_id>[0-9]+)\/[a-z0-9-A-Z_-]+\/reviews$/', $uri_path, $match) == 1) {
                                                                                            $product = ProductsMapper::findOne(['title'], ['id' => $match['product_id']]);
                                                                                            $product['title'] = ucwords($product['title']);
                                                                                            $page_data = [
                                                                                                'title' => ($product['title']) . ' Reviews | Write Up Fitness Review',
                                                                                                'meta_tags' => [
                                                                                                    [
                                                                                                        'name' => 'description',
                                                                                                        'content' => "Read all the reviews and ratings of " . ($product['title']) . "."
                                                                                                    ],
                                                                                                ]
                                                                                            ];
                                                                                        } else {
                                                                                            //REVIEWS PAGE
                                                                                            if (preg_match('/^(reviews)(\/)?$/', $uri_path, $match) == 1) {
                                                                                                $page_data = [
                                                                                                    'title' => 'Read All Reviews | WriteUp Fitness Reviews',
                                                                                                    'meta_tags' => [
                                                                                                        [
                                                                                                            'name' => 'description',
                                                                                                            'content' => "Write Up brings you the honest and unbiased fitness industry product and service reviews from real users. Checkout all the reviews submitted by WriteUp users."
                                                                                                        ],
                                                                                                    ]
                                                                                                ];
                                                                                            } else {
                                                                                                //PRODUCT REVIEW
                                                                                                if (preg_match('/^(product|products)\/(?<product_id>[0-9]+)\/[a-z0-9-A-Z_-]+\/(review|reviews)\/(?<review_id>[0-9]+)(\/.+)?$/', $uri_path, $match) == 1) {
                                                                                                    $product = ProductsMapper::findOne(['title', 'user_id'], ['id' => $match['product_id']]);
                                                                                                    $company = UsersMapper::findOne(['name'], ['id' => $product['user_id']]);
                                                                                                    $review = ReviewsMapper::findOne(['title', 'from_user'], ['id' => $match['review_id']]);
                                                                                                    $review_from_user = UsersMapper::findOne(['name'], ['id' => $review['from_user']]);
                                                                                                    $product['title'] = ucwords($product['title']);
                                                                                                    $company['name'] = ucwords($company['name']);
                                                                                                    $review_from_user['name'] = ucwords($review_from_user['name']);
                                                                                                    $review['title'] = ucwords($review['title']);
//                                                                                                    $page_data['title'] = ucwords($review['title']) . " - " . ucwords($product['title']) . " Detailed Review By " . ucwords($review_from_user['name']) . " | Write Up Fitness Reviews";
                                                                                                    $page_data['title'] = "{$review['title']} Review for {$product['title']} | WriteUp Fitness Review";
                                                                                                    $page_data['meta_tags'][] = [
                                                                                                        'name' => 'description',
//                                                                                                        'content' => "Read and reply to {$review_from_user ['name']} user review for {$product['title']} product of {$company['name']} fitness company. Help others find and discover the best products that are just right for them."
                                                                                                        'content' => "Read detailed review for {$product['title']} by {$review_from_user['name']} user. Help others find and discover the product/services that are just right for them."
                                                                                                    ];
                                                                                                } else {
                                                                                                    if (preg_match('/^(signup-company)$/', $uri_path) == 1) {
                                                                                                        $page_data = [
                                                                                                            'title' => 'Company Signup | WriteUp Fitness Reviews',
                                                                                                        ];
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!isset($page_data['canonical'])) {
            $page_data['canonical'] = $this->config['site_url'] . '/' . $uri_path;
        }

        //Default tags
//        $page_data['meta_tags'][] = [
//            'name' => 'copyright',
//            'content' => $this->config['site_details']['organization_name']
//        ];
//        $page_data['meta_tags'][] = [
//            'name' => 'subject',
//            'content' => $this->config['site_details']['site_tagline']
//        ];

        return $this->generateHtmlData($page_data);
    }

    private function generateHtmlData($structured_data) {
        echo $structured_data;
    }

    private function generateHtmlDatax($page_data) {
        $html = "\n";

        if (!isset($page_data['title'])) {
            $html .= sprintf('<title>%s</title>', $this->config['site_details']['site_title']);
        } else {
            $html .= sprintf('<title>%s</title>', $page_data['title']);
        }

        if (isset($page_data['canonical'])) {
            $html .= "\n";
            $html .= sprintf('<link rel="canonical" href="%s" />', $page_data['canonical']);
        }

        if (isset($page_data['meta_tags']) && !empty($page_data['meta_tags'])) {
            foreach ($page_data['meta_tags'] as $k => $tag) {
                $html .= "\n";
                $html .= sprintf('<meta name="%s" content="%s" />', $tag['name'], $tag['content']);
            }
        }

        $html .= "\n";
        return $html;
    }

}
