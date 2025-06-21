<?php

namespace App\Helpers;

class UserHelper
{
    /**
     * Generate user initials from name
     */
    public static function getInitials($name)
    {
        if (empty($name)) {
            return 'U';
        }

        $words = explode(' ', trim($name));
        $initials = '';

        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[count($words) - 1], 0, 1));
        } else {
            $initials = strtoupper(substr($name, 0, 2));
        }

        return $initials;
    }

    /**
     * Generate a color based on user name for consistent avatar colors
     */
    public static function getAvatarColor($name)
    {
        if (empty($name)) {
            return '#667eea';
        }

        $colors = [
            '#667eea',
            '#764ba2',
            '#f093fb',
            '#f5576c',
            '#4facfe',
            '#00f2fe',
            '#43e97b',
            '#38f9d7',
            '#fa709a',
            '#fee140',
            '#a8edea',
            '#fed6e3',
            '#ffecd2',
            '#fcb69f',
            '#ff9a9e',
            '#fecfef',
            '#fecfef',
            '#fad0c4',
            '#ffd1ff',
            '#a1c4fd'
        ];

        $hash = crc32($name);
        return $colors[abs($hash) % count($colors)];
    }

    /**
     * Generate HTML for user avatar
     */
    public static function getAvatarHtml($user, $size = 'normal', $showName = false)
    {
        $name = $user->name ?? 'User';
        $initials = self::getInitials($name);
        $color = self::getAvatarColor($name);

        $sizeClass = '';
        switch ($size) {
            case 'small':
                $sizeClass = 'small';
                break;
            case 'large':
                $sizeClass = 'large';
                break;
            case 'xlarge':
                $sizeClass = 'xlarge';
                break;
        }

        $html = '<div class="user-avatar ' . $sizeClass . '" style="background: ' . $color . ';">';
        $html .= '<span>' . $initials . '</span>';
        $html .= '</div>';

        if ($showName) {
            $html .= '<div class="ms-2"><div class="fw-medium">' . $name . '</div></div>';
        }

        return $html;
    }

    /**
     * Generate avatar with image if available, fallback to initials
     */
    public static function getAvatarWithImage($user, $size = 'normal', $showName = false)
    {
        $name = $user->name ?? 'User';
        $initials = self::getInitials($name);
        $color = self::getAvatarColor($name);

        $sizeClass = '';
        switch ($size) {
            case 'small':
                $sizeClass = 'small';
                break;
            case 'large':
                $sizeClass = 'large';
                break;
            case 'xlarge':
                $sizeClass = 'xlarge';
                break;
        }

        // Check if user has an avatar image
        if (!empty($user->avatar) || !empty($user->profile_image)) {
            $imageUrl = $user->avatar ?? $user->profile_image;
            $html = '<div class="user-avatar ' . $sizeClass . '">';
            $html .= '<img src="' . $imageUrl . '" alt="' . $name . '" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
            $html .= '<div class="user-avatar ' . $sizeClass . '" style="background: ' . $color . '; display: none;">';
            $html .= '<span>' . $initials . '</span>';
            $html .= '</div>';
            $html .= '</div>';
        } else {
            $html = '<div class="user-avatar ' . $sizeClass . '" style="background: ' . $color . ';">';
            $html .= '<span>' . $initials . '</span>';
            $html .= '</div>';
        }

        if ($showName) {
            $html .= '<div class="ms-2"><div class="fw-medium">' . $name . '</div></div>';
        }

        return $html;
    }
}
