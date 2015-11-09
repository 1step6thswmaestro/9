class ApplicationController < ActionController::Base
  before_filter :set_headers
  protect_from_forgery with: :null_session


  private
  def set_headers
    headers['Access-Control-Allow-Origin'] = '*'
    headers['Access-Control-Allow-Methods'] = 'GET, POST, PATCH, PUT, DELETE, OPTIONS, HEAD'
    headers['Access-Control-Allow-Headers'] = '*'
  end
end
