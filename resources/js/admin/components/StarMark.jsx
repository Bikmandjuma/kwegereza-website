/**
 * The 8-point star (khatam) is a foundational unit in Islamic geometric
 * pattern — built from two overlapping squares. Used here as the one
 * signature mark: the sidebar wordmark, a faint watermark on the login
 * screen and empty states. Line-art only, no color fills, so it stays
 * quiet rather than decorative.
 */
export default function StarMark({ className = 'h-8 w-8', strokeWidth = 1.5 }) {
  return (
    <svg viewBox="0 0 100 100" fill="none" className={className}>
      <rect
        x="20" y="20" width="60" height="60"
        stroke="currentColor" strokeWidth={strokeWidth}
        transform="rotate(0 50 50)"
      />
      <rect
        x="20" y="20" width="60" height="60"
        stroke="currentColor" strokeWidth={strokeWidth}
        transform="rotate(45 50 50)"
      />
    </svg>
  )
}
