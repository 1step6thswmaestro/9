Rails.application.routes.draw do
  resources :reactions
  resources :user_actions, only: :create
end
