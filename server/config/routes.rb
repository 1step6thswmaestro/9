Rails.application.routes.draw do
  resources :purchases, only: :create
  resources :scores, only: :create
  resources :reactions, only: [:index, :create, :destroy]
  resources :user_actions, only: :create
end
