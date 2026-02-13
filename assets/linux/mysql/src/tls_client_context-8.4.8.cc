stdx::expected<SSL_SESSION *, std::error_code> TlsClientContext::get_session() {
  // sessions_ will be nullptr if caching is off
  if (sessions_) {
    std::lock_guard lk(sessions_->mtx_);
    auto &sessions = sessions_->sessions_;
    for (auto it = sessions.cbegin(); it != sessions.cend();) {
      const auto sess = it->second.get();
// [!code focus:5]
#if OPENSSL_VERSION_NUMBER >= 0x30400000L
      const auto sess_start = SSL_SESSION_get_time_ex(sess);
#else
      const auto sess_start = SSL_SESSION_get_time(sess);
#endif
      if (time(nullptr) - sess_start > session_cache_timeout_.count()) {
        // session expired, remove from cache
        sessions.erase(it++);
        continue;
      }
      if (SSL_SESSION_is_resumable_wrapper(sess)) {
        return sess;
      }
      ++it;
    }
  }

  return stdx::unexpected(
      make_error_code(std::errc::no_such_file_or_directory));
}