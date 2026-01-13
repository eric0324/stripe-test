<?php

return [
    // 頁面標題
    "title" => "결제",
    "subtitle" => "결제 방식을 선택해 주세요",

    // 方案選擇
    "plan" => [
        "onetime" => [
            "name" => "일시불",
            "detail" => "쿠폰 사용 가능",
        ],
        "installment" => [
            "name" => "12개월 할부",
            "price_suffix" => "/월",
            "detail_prefix" => "총 ₩",
        ],
    ],

    // 表單欄位
    "form" => [
        "email" => "이메일",
        "email_placeholder" => "example@email.com",
        "coupon" => "쿠폰 코드",
        "coupon_placeholder" => "쿠폰 코드를 입력하세요",
        "apply" => "적용",
        "checking" => "확인 중...",
    ],

    // 價格摘要
    "price" => [
        "subtotal" => "소계",
        "discount" => "할인",
        "total" => "합계",
        "monthly" => "/월",
        "monthly_format" => "₩:price x 12개월",
    ],

    // 按鈕
    "button" => [
        "pay" => "₩:amount 결제하기",
        "subscribe" => "월 ₩:amount 구독 시작",
        "complete" => "결제 완료",
        "start_subscription" => "구독 시작",
        "processing" => "처리 중...",
    ],

    // 訊息
    "message" => [
        "coupon_applied" => "쿠폰이 적용되었습니다! ₩:amount 할인",
        "coupon_invalid" => "유효하지 않은 쿠폰 코드입니다",
        "coupon_error" => "쿠폰 확인에 실패했습니다",
        "email_required" => "이메일을 입력해 주세요",
        "payment_error" => "결제 처리 중 오류가 발생했습니다",
    ],

    // 成功頁面
    "success" => [
        "title" => "결제가 완료되었습니다!",
        "onetime_badge" => "일시불 결제",
        "installment_badge" => "12개월 구독",
        "onetime_message" =>
            "결제가 성공적으로 처리되었습니다.\n결제 확인 이메일이 발송되었습니다.\n감사합니다.",
        "installment_message" =>
            "12개월 구독이 신청되었습니다.\n해당 기간동안 매월 비용이 청구됩니다.\n결제 확인 이메일이 발송되었습니다.",
        "back_home" => "홈으로 돌아가기",
    ],
];
