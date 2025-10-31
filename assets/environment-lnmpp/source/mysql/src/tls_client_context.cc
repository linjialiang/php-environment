// 修改 mysql-8.4.6/router/src/harness/src/tls_client_context.cc 第220行
stdx::expected<SSL_SESSION *, std::error_code> TlsClientContext::get_session() {
  // sessions_ will be nullptr if caching is off
  if (sessions_) {
    std::lock_guard lk(sessions_->mtx_);
    auto &sessions = sessions_->sessions_;
    for (auto it = sessions.cbegin(); it != sessions.cend();) {
      const auto sess = it->second.get();
      // [!code focus:2]
      const auto sess_start = SSL_SESSION_get_time(sess); // [!code --]
      const auto sess_start = SSL_SESSION_get_time_ex(sess); // [!code ++]
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