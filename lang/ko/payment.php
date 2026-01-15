<?php

return [
    // 頁面標題
    "title" => "전신 바디라인 리모델링 프로젝트",
    "subtitle" => "결제 방식을 선택해 주세요",
    "course_title" => "VOD 12개월 수강권",
    "course_info" => [
        "총 36개 단원 · 20시간 · VOD 사전 녹화 강의",
        "본 강의는 예약 구매 방식으로 진행되며, 영상은 순차적으로 업로드됩니다",
        "※ 2026년 2월 15일 전체 강의 업로드 완료 예정",
        "구매일로부터 1년간 무제한 시청 가능",
    ],
    "upload_schedule" => [
        "title" => "본 강의는 예약 구매 방식으로 진행되며, 영상은 순차적으로 업로드됩니다 :",
        "items" => [
            "CH0, CH1, CH2, CH3: 업로드 완료, 시청 가능",
            "CH3 따라하기 영상: 2026/1/15 업로드 예정",
            "CH4+CH4 따라하기 영상: 2026/1/30 업로드 예정",
            "CH5, CH6: 2026/2/15 업로드 예정",
        ],
    ],

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
        "email_hint" => "gmail 사용을 권장합니다. 일부 이메일 도메인은 수신이 어려울 수 있습니다(예: icloud 등)",
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

    // 失敗頁面
    "failed" => [
        "title" => "결제가 실패했습니다",
        "message" => "결제 처리 중 문제가 발생했습니다.\n다시 시도해 주세요.",
        "back_payment" => "다시 시도",
    ],
];
