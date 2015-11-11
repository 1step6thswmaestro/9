Rails.application.routes.draw do
  resources :user_actions, only: :create
end
